<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\Interaction;
use App\Helpers\Slug;
use App\Helpers\Security;

class ChapterController extends Controller {
    private Chapter $chapterModel;
    private Story $storyModel;

    public function __construct() {
        parent::__construct();
        $this->chapterModel = new Chapter();
        $this->storyModel = new Story();
    }

    public function getByStory(int $storyId): void {
        $onlyPublished = !Auth::isAdmin();
        $order = $this->request->get('order', 'ASC');
        $chapters = $this->chapterModel->getByStoryId($storyId, $order === 'DESC' ? 'DESC' : 'ASC', $onlyPublished);
        $this->success($chapters);
    }

    public function read(string $storySlug, string $chapterSlug): void {
        $chapter = $this->chapterModel->getChapterForReading($storySlug, $chapterSlug);
        if (!$chapter) {
            $this->error('Không tìm thấy chương truyện.', 404);
        }

        // Increment views
        $this->storyModel->incrementViews($chapter['story_id'], $chapter['id']);

        // Auto save reading history if user is logged in
        $userId = Auth::id();
        if ($userId) {
            $interaction = new Interaction();
            $progress = (int)$this->request->get('progress', 0);
            $interaction->saveHistory($userId, $chapter['story_id'], $chapter['id'], $progress);
        }

        $this->success($chapter);
    }

    public function store(): void {
        Auth::requireAdmin();
        $data = $this->request->all();

        $storyId = (int)($data['story_id'] ?? 0);
        $title = trim($data['title'] ?? '');
        $content = Security::cleanRichText($data['content'] ?? '');
        $status = in_array($data['status'] ?? '', ['published', 'draft']) ? $data['status'] : 'published';

        if ($storyId <= 0 || empty($title) || empty($content)) {
            $this->error('Vui lòng nhập đầy đủ Story ID, tiêu đề chương và nội dung.');
        }

        $chapterNumber = isset($data['chapter_number']) && is_numeric($data['chapter_number'])
            ? (float)$data['chapter_number']
            : $this->chapterModel->getNextChapterNumber($storyId);

        $slug = !empty($data['slug']) ? Slug::create($data['slug']) : Slug::create("chuong-{$chapterNumber}-" . $title);

        $chapterId = $this->chapterModel->create([
            'story_id'       => $storyId,
            'chapter_number' => $chapterNumber,
            'title'          => $title,
            'slug'           => $slug,
            'content'        => $content,
            'views_count'    => 0,
            'status'         => $status
        ]);

        // Touch story updated_at
        $this->storyModel->update($storyId, ['updated_at' => date('Y-m-d H:i:s')]);

        $created = $this->chapterModel->find($chapterId);
        $this->success($created, 'Thêm chương mới thành công!', 201);
    }

    public function update(int $id): void {
        Auth::requireAdmin();
        $chapter = $this->chapterModel->find($id);
        if (!$chapter) {
            $this->error('Không tìm thấy chương cần sửa.', 404);
        }

        $data = $this->request->all();
        $updateData = [];

        if (isset($data['title'])) {
            $updateData['title'] = trim($data['title']);
        }

        if (isset($data['chapter_number'])) {
            $updateData['chapter_number'] = (float)$data['chapter_number'];
        }

        if (isset($data['slug'])) {
            $updateData['slug'] = Slug::create($data['slug']);
        } elseif (isset($data['title'])) {
            $num = $updateData['chapter_number'] ?? $chapter['chapter_number'];
            $updateData['slug'] = Slug::create("chuong-{$num}-" . $updateData['title']);
        }

        if (isset($data['content'])) {
            $updateData['content'] = Security::cleanRichText($data['content']);
        }

        if (isset($data['status']) && in_array($data['status'], ['published', 'draft'])) {
            $updateData['status'] = $data['status'];
        }

        if (!empty($updateData)) {
            $this->chapterModel->update($id, $updateData);
            $this->storyModel->update($chapter['story_id'], ['updated_at' => date('Y-m-d H:i:s')]);
        }

        $updated = $this->chapterModel->find($id);
        $this->success($updated, 'Cập nhật chương thành công!');
    }

    public function destroy(int $id): void {
        Auth::requireAdmin();
        $chapter = $this->chapterModel->find($id);
        if (!$chapter) {
            $this->error('Không tìm thấy chương cần xóa.', 404);
        }

        $this->chapterModel->delete($id);
        $this->success(null, 'Đã xóa chương thành công!');
    }
}
