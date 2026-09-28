<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Category;
use App\Helpers\Slug;

class CategoryController extends Controller {
    private Category $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    public function index(): void {
        $categories = $this->categoryModel->getAllWithStoryCount();
        $this->success($categories);
    }

    public function show(string $slug): void {
        $category = $this->categoryModel->findBySlug($slug);
        if (!$category) {
            $this->error('Không tìm thấy thể loại.', 404);
        }
        $this->success($category);
    }

    public function store(): void {
        Auth::requireAdmin();
        $data = $this->request->all();
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        if (empty($name)) {
            $this->error('Tên thể loại không được để trống.');
        }

        $slug = !empty($data['slug']) ? Slug::create($data['slug']) : Slug::create($name);

        if ($this->categoryModel->findBySlug($slug)) {
            $this->error('Slug thể loại đã tồn tại.');
        }

        $id = $this->categoryModel->create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description
        ]);

        $this->success($this->categoryModel->find($id), 'Thêm thể loại thành công!', 201);
    }

    public function update(int $id): void {
        Auth::requireAdmin();
        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->error('Không tìm thấy thể loại.', 404);
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
        if (isset($data['description'])) {
            $updateData['description'] = trim($data['description']);
        }

        if (!empty($updateData)) {
            $this->categoryModel->update($id, $updateData);
        }

        $this->success($this->categoryModel->find($id), 'Cập nhật thể loại thành công!');
    }

    public function destroy(int $id): void {
        Auth::requireAdmin();
        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->error('Không tìm thấy thể loại.', 404);
        }

        $this->categoryModel->delete($id);
        $this->success(null, 'Đã xóa thể loại!');
    }
}
