<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Story;
use App\Models\Interaction;
use App\Helpers\Slug;
use App\Helpers\Security;

class StoryController extends Controller {
    private Story $storyModel;

    public function __construct() {
        parent::__construct();
        $this->storyModel = new Story();
    }

    public function index(): void {
        $params = $this->request->query();
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = max(1, min(50, (int)($params['limit'] ?? 12)));

        $filters = [
            'category' => $params['category'] ?? null,
            'author'   => $params['author'] ?? null,
            'status'   => $params['status'] ?? null,
            'q'        => $params['q'] ?? null,
            'sort'     => $params['sort'] ?? 'updated'
        ];

        $result = $this->storyModel->listStories($filters, $page, $limit);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }

    public function show(string $slug): void {
        $story = $this->storyModel->findBySlug($slug);
        if (!$story) {
            $this->error('Không tìm thấy truyện.', 404);
        }

        // Add user interaction flags if logged in
        $userId = Auth::id();
        if ($userId) {
            $interaction = new Interaction();
            $story['is_following'] = $interaction->isFollowing($userId, $story['id']);
            $story['is_favorited'] = $interaction->isFavorited($userId, $story['id']);
        } else {
            $story['is_following'] = false;
            $story['is_favorited'] = false;
        }

        $this->success($story);
    }

    public function store(): void {
        Auth::requireAdmin();
        $data = $this->request->all();

        $title = trim($data['title'] ?? '');
        $description = Security::cleanRichText($data['description'] ?? '');
        $authorId = !empty($data['author_id']) ? (int)$data['author_id'] : null;
        $status = in_array($data['status'] ?? '', ['updating', 'completed', 'paused']) ? $data['status'] : 'updating';
        $categories = $data['categories'] ?? [];

        if (empty($title)) {
            $this->error('Tiêu đề truyện không được để trống.');
        }

        $slug = !empty($data['slug']) ? Slug::create($data['slug']) : Slug::create($title);

        // Check unique slug
        if ($this->storyModel->findBySlug($slug)) {
            $slug .= '-' . time();
        }

        $coverImage = 'default-cover.jpg';
        if (isset($_FILES['cover'])) {
            $uploaded = Security::handleUpload($_FILES['cover'], 'covers');
            if ($uploaded) {
                $coverImage = $uploaded;
            }
        } elseif (!empty($data['cover_image'])) {
            $coverImage = trim($data['cover_image']);
        }

        $storyId = $this->storyModel->create([
            'title'       => $title,
            'slug'        => $slug,
            'description' => $description,
            'author_id'   => $authorId,
            'cover_image' => $coverImage,
            'status'      => $status,
            'views_count' => 0,
            'rating_avg'  => 0.00,
            'rating_count'=> 0
        ]);

        if (!empty($categories) && is_array($categories)) {
            $this->storyModel->syncCategories($storyId, $categories);
        }

        $created = $this->storyModel->find($storyId);
        $this->success($created, 'Thêm truyện mới thành công!', 201);
    }

    public function update(int $id): void {
        Auth::requireAdmin();
        $story = $this->storyModel->find($id);
        if (!$story) {
            $this->error('Không tìm thấy truyện cần sửa.', 404);
        }

        $data = $this->request->all();
        $updateData = [];

        if (isset($data['title'])) {
            $updateData['title'] = trim($data['title']);
            if (empty($data['slug'])) {
                $updateData['slug'] = Slug::create($updateData['title']);
            }
        }

        if (isset($data['slug'])) {
            $updateData['slug'] = Slug::create($data['slug']);
        }

        if (isset($data['description'])) {
            $updateData['description'] = Security::cleanRichText($data['description']);
        }

        if (isset($data['author_id'])) {
            $updateData['author_id'] = !empty($data['author_id']) ? (int)$data['author_id'] : null;
        }

        if (isset($data['status']) && in_array($data['status'], ['updating', 'completed', 'paused'])) {
            $updateData['status'] = $data['status'];
        }

        if (isset($_FILES['cover'])) {
            $uploaded = Security::handleUpload($_FILES['cover'], 'covers');
            if ($uploaded) {
                $updateData['cover_image'] = $uploaded;
            }
        } elseif (!empty($data['cover_image'])) {
            $updateData['cover_image'] = trim($data['cover_image']);
        }

        if (!empty($updateData)) {
            $this->storyModel->update($id, $updateData);
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            $this->storyModel->syncCategories($id, $data['categories']);
        }

        $updated = $this->storyModel->findBySlug($updateData['slug'] ?? $story['slug']);
        $this->success($updated, 'Cập nhật truyện thành công!');
    }

    public function destroy(int $id): void {
        Auth::requireAdmin();
        $story = $this->storyModel->find($id);
        if (!$story) {
            $this->error('Không tìm thấy truyện cần xóa.', 404);
        }

        $this->storyModel->delete($id);
        $this->success(null, 'Đã xóa truyện thành công!');
    }

    public function view(int $id): void {
        $this->storyModel->incrementViews($id);
        $this->success(null, 'Views counted');
    }
}
