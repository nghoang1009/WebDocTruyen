<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Author;
use App\Helpers\Slug;

class AuthorController extends Controller {
    private Author $authorModel;

    public function __construct() {
        parent::__construct();
        $this->authorModel = new Author();
    }

    public function index(): void {
        $authors = $this->authorModel->getAllWithStoryCount();
        $this->success($authors);
    }

    public function show(string $slug): void {
        $author = $this->authorModel->findBySlug($slug);
        if (!$author) {
            $this->error('Không tìm thấy tác giả.', 404);
        }
        $this->success($author);
    }

    public function store(): void {
        Auth::requireAdmin();
        $data = $this->request->all();
        $name = trim($data['name'] ?? '');
        $bio = trim($data['bio'] ?? '');

        if (empty($name)) {
            $this->error('Tên tác giả không được để trống.');
        }

        $slug = !empty($data['slug']) ? Slug::create($data['slug']) : Slug::create($name);

        if ($this->authorModel->findBySlug($slug)) {
            $this->error('Tác giả này đã tồn tại.');
        }

        $id = $this->authorModel->create([
            'name' => $name,
            'slug' => $slug,
            'bio'  => $bio
        ]);

        $this->success($this->authorModel->find($id), 'Thêm tác giả thành công!', 201);
    }

    public function update(int $id): void {
        Auth::requireAdmin();
        $author = $this->authorModel->find($id);
        if (!$author) {
            $this->error('Không tìm thấy tác giả.', 404);
        }

        $data = $this->request->all();
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = trim($data['name']);
            if (empty($data['slug'])) {
                $updateData['slug'] = Slug::create($updateData['name']);
            }
        }
        if (isset($data['slug'])) {
            $updateData['slug'] = Slug::create($data['slug']);
        }
        if (isset($data['bio'])) {
            $updateData['bio'] = trim($data['bio']);
        }

        if (!empty($updateData)) {
            $this->authorModel->update($id, $updateData);
        }

        $this->success($this->authorModel->find($id), 'Cập nhật tác giả thành công!');
    }

    public function destroy(int $id): void {
        Auth::requireAdmin();
        $author = $this->authorModel->find($id);
        if (!$author) {
            $this->error('Không tìm thấy tác giả.', 404);
        }

        $this->authorModel->delete($id);
        $this->success(null, 'Đã xóa tác giả!');
    }
}
