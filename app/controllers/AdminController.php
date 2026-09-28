<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Stats;
use App\Models\User;
use App\Models\Comment;

class AdminController extends Controller {
    private Stats $statsModel;
    private User $userModel;
    private Comment $commentModel;

    public function __construct() {
        parent::__construct();
        $this->statsModel = new Stats();
        $this->userModel = new User();
        $this->commentModel = new Comment();
    }

    public function dashboard(): void {
        Auth::requireAdmin();
        $overview = $this->statsModel->getOverview();
        $dailyViews = $this->statsModel->getDailyViews(7);
        $dailyUsers = $this->statsModel->getDailyNewUsers(7);
        $topStories = $this->statsModel->getTopStories(5);
        $topChapters = $this->statsModel->getTopChapters(5);

        $this->success([
            'overview'     => $overview,
            'daily_views'  => $dailyViews,
            'daily_users'  => $dailyUsers,
            'top_stories'  => $topStories,
            'top_chapters' => $topChapters
        ]);
    }

    // ================= USER MANAGEMENT =================
    public function users(): void {
        Auth::requireAdmin();
        $page = max(1, (int)$this->request->get('page', 1));
        $limit = max(1, min(50, (int)$this->request->get('limit', 20)));
        $search = trim($this->request->get('search', ''));

        $result = $this->userModel->getAllUsersWithRoles($page, $limit, $search);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }

    public function updateUser(int $id): void {
        $admin = Auth::requireAdmin();
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Không tìm thấy người dùng.', 404);
        }

        $data = $this->request->all();
        $updateData = [];

        if (isset($data['status']) && in_array($data['status'], ['active', 'banned'])) {
            // Prevent self-ban
            if ((int)$admin['id'] === $id && $data['status'] === 'banned') {
                $this->error('Bạn không thể tự khóa tài khoản của chính mình.');
            }
            $updateData['status'] = $data['status'];
        }

        if (isset($data['role_id']) && in_array((int)$data['role_id'], [1, 2])) {
            // Prevent self-demotion
            if ((int)$admin['id'] === $id && (int)$data['role_id'] !== 1) {
                $this->error('Bạn không thể tự hạ quyền admin của mình.');
            }
            $updateData['role_id'] = (int)$data['role_id'];
        }

        if (!empty($updateData)) {
            $this->userModel->update($id, $updateData);
        }

        $updatedUser = $this->userModel->getUserWithRole($id);
        $this->success($updatedUser, 'Cập nhật tài khoản thành công!');
    }

    public function deleteUser(int $id): void {
        $admin = Auth::requireAdmin();
        if ((int)$admin['id'] === $id) {
            $this->error('Bạn không thể xóa tài khoản của chính mình.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error('Không tìm thấy người dùng.', 404);
        }

        $this->userModel->delete($id);
        $this->success(null, 'Đã xóa người dùng thành công!');
    }

    // ================= COMMENT MODERATION =================
    public function comments(): void {
        Auth::requireAdmin();
        $page = max(1, (int)$this->request->get('page', 1));
        $limit = max(1, min(50, (int)$this->request->get('limit', 20)));
        $search = trim($this->request->get('search', ''));

        $result = $this->commentModel->getAllAdmin($page, $limit, $search);
        $this->paginate($result['items'], $result['total'], $page, $limit);
    }
}
