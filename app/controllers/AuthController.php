<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Models\User;
use App\Helpers\Security;

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function register(): void {
        $data = $this->request->all();
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirmPassword = $data['password_confirmation'] ?? $data['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $this->error('Vui lòng điền đầy đủ tất cả các trường.');
        }

        if (strlen($username) < 3 || strlen($username) > 30) {
            $this->error('Tên tài khoản phải từ 3 đến 30 ký tự.');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $this->error('Tên tài khoản chỉ được chứa chữ cái, số và dấu gạch dưới.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email không hợp lệ.');
        }

        if (strlen($password) < 6) {
            $this->error('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        if (!empty($confirmPassword) && $password !== $confirmPassword) {
            $this->error('Xác nhận mật khẩu không khớp.');
        }

        if ($this->userModel->findByUsername($username)) {
            $this->error('Tên tài khoản đã tồn tại.');
        }

        if ($this->userModel->findByEmail($email)) {
            $this->error('Email đã được sử dụng.');
        }

        $hashedPassword = Auth::hashPassword($password);
        $userId = $this->userModel->create([
            'role_id'  => 2, // Standard user
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword,
            'avatar'   => 'default-avatar.png',
            'status'   => 'active'
        ]);

        $user = $this->userModel->getUserWithRole($userId);
        $token = Auth::generateToken(['sub' => $userId, 'role' => $user['role_name']]);
        Auth::loginSession($user);

        // Set auth cookie
        setcookie('auth_token', $token, time() + SESSION_LIFETIME, '/', '', false, true);

        $this->success([
            'user'  => $user,
            'token' => $token
        ], 'Đăng ký tài khoản thành công!');
    }

    public function login(): void {
        $data = $this->request->all();
        $account = trim($data['username'] ?? $data['account'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($account) || empty($password)) {
            $this->error('Vui lòng nhập tài khoản và mật khẩu.');
        }

        $user = str_contains($account, '@')
            ? $this->userModel->findByEmail($account)
            : $this->userModel->findByUsername($account);

        if (!$user) {
            $this->error('Tài khoản hoặc mật khẩu không chính xác.', 401);
        }

        if ($user['status'] === 'banned') {
            $this->error('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.', 403);
        }

        if (!Auth::verifyPassword($password, $user['password'])) {
            $this->error('Tài khoản hoặc mật khẩu không chính xác.', 401);
        }

        // Update last login
        $this->userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        $userData = $this->userModel->getUserWithRole($user['id']);
        $token = Auth::generateToken(['sub' => $user['id'], 'role' => $userData['role_name']]);
        Auth::loginSession($userData);

        setcookie('auth_token', $token, time() + SESSION_LIFETIME, '/', '', false, true);

        $this->success([
            'user'  => $userData,
            'token' => $token
        ], 'Đăng nhập thành công!');
    }

    public function logout(): void {
        Auth::logout();
        $this->success(null, 'Đăng xuất thành công!');
    }

    public function me(): void {
        $user = Auth::user();
        if (!$user) {
            $this->error('Chưa đăng nhập', 401);
        }
        $this->success($user);
    }

    public function updateProfile(): void {
        $user = Auth::requireAuth();
        $data = $this->request->all();

        $updateData = [];
        if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $existing = $this->userModel->findByEmail($data['email']);
            if ($existing && (int)$existing['id'] !== (int)$user['id']) {
                $this->error('Email này đã được tài khoản khác sử dụng.');
            }
            $updateData['email'] = trim($data['email']);
        }

        if (isset($_FILES['avatar'])) {
            $uploaded = Security::handleUpload($_FILES['avatar'], 'avatars');
            if ($uploaded) {
                $updateData['avatar'] = $uploaded;
            }
        }

        if (!empty($updateData)) {
            $this->userModel->update($user['id'], $updateData);
        }

        $updatedUser = $this->userModel->getUserWithRole($user['id']);
        Auth::loginSession($updatedUser);

        $this->success($updatedUser, 'Cập nhật thông tin thành công!');
    }

    public function changePassword(): void {
        $user = Auth::requireAuth();
        $data = $this->request->all();
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            $this->error('Vui lòng điền đầy đủ mật khẩu cũ và mới.');
        }

        $fullUser = $this->userModel->find($user['id']);
        if (!Auth::verifyPassword($currentPassword, $fullUser['password'])) {
            $this->error('Mật khẩu hiện tại không đúng.');
        }

        if (strlen($newPassword) < 6) {
            $this->error('Mật khẩu mới phải có ít nhất 6 ký tự.');
        }

        if (!empty($confirmPassword) && $newPassword !== $confirmPassword) {
            $this->error('Xác nhận mật khẩu mới không khớp.');
        }

        $this->userModel->update($user['id'], [
            'password' => Auth::hashPassword($newPassword)
        ]);

        $this->success(null, 'Đổi mật khẩu thành công!');
    }
}
