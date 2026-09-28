<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Interaction;
use App\Helpers\Security;

class InteractionController extends Controller {
    private Comment $commentModel;
    private Rating $ratingModel;
    private Interaction $interactionModel;

    public function __construct() {
        parent::__construct();
        $this->commentModel = new Comment();
        $this->ratingModel = new Rating();
        $this->interactionModel = new Interaction();
    }

    // ================= COMMENTS =================
    public function getComments(int $storyId): void {
        $page = max(1, (int)$this->request->get('page', 1));
        $limit = max(1, min(50, (int)$this->request->get('limit', 20)));
        $result = $this->commentModel->getByStory($storyId, $page, $limit);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }

    public function addComment(int $storyId): void {
        $user = Auth::requireAuth();
        $data = $this->request->all();
        $content = Security::clean(trim($data['content'] ?? ''));
        $chapterId = !empty($data['chapter_id']) ? (int)$data['chapter_id'] : null;
        $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;

        if (empty($content)) {
            $this->error('Nội dung bình luận không được để trống.');
        }

        $commentId = $this->commentModel->create([
            'user_id'    => $user['id'],
            'story_id'   => $storyId,
            'chapter_id' => $chapterId,
            'parent_id'  => $parentId,
            'content'    => $content
        ]);

        $newComment = $this->commentModel->find($commentId);
        $newComment['username'] = $user['username'];
        $newComment['avatar'] = $user['avatar'];
        $newComment['role_name'] = $user['role_name'];

        $this->success($newComment, 'Bình luận đã được đăng thành công!', 201);
    }

    public function updateComment(int $id): void {
        $user = Auth::requireAuth();
        $comment = $this->commentModel->find($id);

        if (!$comment) {
            $this->error('Không tìm thấy bình luận.', 404);
        }

        // Only owner or admin can edit
        if ((int)$comment['user_id'] !== (int)$user['id'] && !Auth::isAdmin()) {
            $this->error('Bạn không có quyền chỉnh sửa bình luận này.', 403);
        }

        $data = $this->request->all();
        $content = Security::clean(trim($data['content'] ?? ''));

        if (empty($content)) {
            $this->error('Nội dung bình luận không được để trống.');
        }

        $this->commentModel->update($id, [
            'content'    => $content,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->success($this->commentModel->find($id), 'Cập nhật bình luận thành công!');
    }

    public function deleteComment(int $id): void {
        $user = Auth::requireAuth();
        $comment = $this->commentModel->find($id);

        if (!$comment) {
            $this->error('Không tìm thấy bình luận.', 404);
        }

        // Only owner or admin can delete
        if ((int)$comment['user_id'] !== (int)$user['id'] && !Auth::isAdmin()) {
            $this->error('Bạn không có quyền xóa bình luận này.', 403);
        }

        $this->commentModel->delete($id);
        $this->success(null, 'Đã xóa bình luận!');
    }

    // ================= RATINGS =================
    public function rate(int $storyId): void {
        $user = Auth::requireAuth();
        $data = $this->request->all();
        $rating = (int)($data['rating'] ?? 0);

        if ($rating < 1 || $rating > 5) {
            $this->error('Đánh giá phải từ 1 đến 5 sao.');
        }

        $result = $this->ratingModel->setRating($user['id'], $storyId, $rating);
        $this->success($result, 'Đánh giá truyện thành công!');
    }

    public function getUserRating(int $storyId): void {
        $userId = Auth::id();
        if (!$userId) {
            $this->success(['user_rating' => null]);
            return;
        }
        $rating = $this->ratingModel->getUserRating($userId, $storyId);
        $this->success(['user_rating' => $rating]);
    }

    // ================= FOLLOWS & FAVORITES =================
    public function toggleFollow(int $storyId): void {
        $user = Auth::requireAuth();
        $isFollowing = $this->interactionModel->toggleFollow($user['id'], $storyId);
        $this->success([
            'is_following' => $isFollowing
        ], $isFollowing ? 'Đã theo dõi truyện!' : 'Đã hủy theo dõi truyện.');
    }

    public function getFollowing(): void {
        $user = Auth::requireAuth();
        $page = max(1, (int)$this->request->get('page', 1));
        $limit = max(1, min(50, (int)$this->request->get('limit', 12)));
        $result = $this->interactionModel->getFollowing($user['id'], $page, $limit);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }

    public function toggleFavorite(int $storyId): void {
        $user = Auth::requireAuth();
        $isFavorited = $this->interactionModel->toggleFavorite($user['id'], $storyId);
        $this->success([
            'is_favorited' => $isFavorited
        ], $isFavorited ? 'Đã thêm vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích.');
    }

    // ================= READING HISTORY =================
    public function getHistory(): void {
        $user = Auth::requireAuth();
        $page = max(1, (int)$this->request->get('page', 1));
        $limit = max(1, min(50, (int)$this->request->get('limit', 20)));
        $result = $this->interactionModel->getHistory($user['id'], $page, $limit);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }

    public function saveHistory(): void {
        $user = Auth::requireAuth();
        $data = $this->request->all();
        $storyId = (int)($data['story_id'] ?? 0);
        $chapterId = (int)($data['chapter_id'] ?? 0);
        $progress = (int)($data['progress'] ?? 0);

        if ($storyId <= 0 || $chapterId <= 0) {
            $this->error('Thông tin lịch sử đọc không hợp lệ.');
        }

        $this->interactionModel->saveHistory($user['id'], $storyId, $chapterId, $progress);
        $this->success(null, 'Đã lưu tiến độ đọc.');
    }

    public function deleteHistoryItem(int $storyId): void {
        $user = Auth::requireAuth();
        $this->interactionModel->deleteHistoryItem($user['id'], $storyId);
        $this->success(null, 'Đã xóa truyện khỏi lịch sử đọc.');
    }

    public function clearHistory(): void {
        $user = Auth::requireAuth();
        $this->interactionModel->clearHistory($user['id']);
        $this->success(null, 'Đã xóa toàn bộ lịch sử đọc.');
    }
}
