/**
 * WebDocTruyen - Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initAdminPage();
});

function initAdminPage() {
    initStoryAdmin();
    initChapterAdmin();
    initCategoryAdmin();
    initAuthorAdmin();
    initUserAdmin();
    initCommentAdmin();
}

// ================= STORY ADMIN =================
function initStoryAdmin() {
    const storyForm = document.getElementById('admin-story-form');
    if (!storyForm) return;

    storyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(storyForm);
        const storyId = document.getElementById('story-id-input')?.value;
        const endpoint = storyId ? `/stories/${storyId}` : '/stories';

        try {
            const res = await API.post(endpoint, formData);
            Toast.success(res.message);
            closeModal('modal-story-form');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    // Delete story
    document.querySelectorAll('.btn-delete-story').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const title = btn.dataset.title || 'truyện này';
            if (!confirm(`Bạn có chắc chắn muốn xóa "${title}" và toàn bộ chương liên quan?`)) return;

            try {
                const res = await API.delete(`/stories/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

window.openEditStoryModal = function(story) {
    document.getElementById('modal-story-title').innerText = 'Chỉnh sửa truyện';
    document.getElementById('story-id-input').value = story.id;
    document.getElementById('story-title-input').value = story.title;
    document.getElementById('story-slug-input').value = story.slug;
    document.getElementById('story-author-input').value = story.author_id || '';
    document.getElementById('story-status-input').value = story.status;
    document.getElementById('story-desc-input').value = story.description;

    // Check categories
    const catIds = (story.categories || []).map(c => c.id.toString());
    document.querySelectorAll('.story-cat-checkbox').forEach(cb => {
        cb.checked = catIds.includes(cb.value);
    });

    openModal('modal-story-form');
};

window.openCreateStoryModal = function() {
    document.getElementById('modal-story-title').innerText = 'Thêm truyện mới';
    document.getElementById('admin-story-form').reset();
    document.getElementById('story-id-input').value = '';
    openModal('modal-story-form');
};

// ================= CHAPTER ADMIN =================
function initChapterAdmin() {
    const chapterForm = document.getElementById('admin-chapter-form');
    if (!chapterForm) return;

    chapterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            story_id: document.getElementById('chapter-story-id-input').value,
            chapter_number: document.getElementById('chapter-number-input').value,
            title: document.getElementById('chapter-title-input').value,
            slug: document.getElementById('chapter-slug-input').value,
            status: document.getElementById('chapter-status-input').value,
            content: document.getElementById('chapter-content-input').value
        };

        const chapterId = document.getElementById('chapter-id-input').value;
        const endpoint = chapterId ? `/chapters/${chapterId}` : '/chapters';

        try {
            const res = await API.post(endpoint, data);
            Toast.success(res.message);
            closeModal('modal-chapter-form');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    // Delete chapter
    document.querySelectorAll('.btn-delete-chapter').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc chắn muốn xóa chương này?')) return;

            try {
                const res = await API.delete(`/chapters/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

window.openEditChapterModal = function(chapter) {
    document.getElementById('modal-chapter-title').innerText = 'Chỉnh sửa chương';
    document.getElementById('chapter-id-input').value = chapter.id;
    document.getElementById('chapter-story-id-input').value = chapter.story_id;
    document.getElementById('chapter-number-input').value = chapter.chapter_number;
    document.getElementById('chapter-title-input').value = chapter.title;
    document.getElementById('chapter-slug-input').value = chapter.slug;
    document.getElementById('chapter-status-input').value = chapter.status;
    document.getElementById('chapter-content-input').value = chapter.content || '';
    openModal('modal-chapter-form');
};

window.openCreateChapterModal = function(storyId = '') {
    document.getElementById('modal-chapter-title').innerText = 'Thêm chương mới';
    document.getElementById('admin-chapter-form').reset();
    document.getElementById('chapter-id-input').value = '';
    if (storyId) {
        document.getElementById('chapter-story-id-input').value = storyId;
    }
    openModal('modal-chapter-form');
};

// ================= CATEGORY ADMIN =================
function initCategoryAdmin() {
    const form = document.getElementById('admin-category-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('cat-id-input').value;
        const data = {
            name: document.getElementById('cat-name-input').value,
            slug: document.getElementById('cat-slug-input').value,
            description: document.getElementById('cat-desc-input').value
        };

        const endpoint = id ? `/categories/${id}` : '/categories';
        try {
            const res = await API.post(endpoint, data);
            Toast.success(res.message);
            closeModal('modal-category-form');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    document.querySelectorAll('.btn-delete-cat').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa thể loại này?')) return;
            try {
                const res = await API.delete(`/categories/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

window.openEditCategoryModal = function(cat) {
    document.getElementById('cat-id-input').value = cat.id;
    document.getElementById('cat-name-input').value = cat.name;
    document.getElementById('cat-slug-input').value = cat.slug;
    document.getElementById('cat-desc-input').value = cat.description || '';
    openModal('modal-category-form');
};

// ================= AUTHOR ADMIN =================
function initAuthorAdmin() {
    const form = document.getElementById('admin-author-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('author-id-input').value;
        const data = {
            name: document.getElementById('author-name-input').value,
            slug: document.getElementById('author-slug-input').value,
            bio: document.getElementById('author-bio-input').value
        };

        const endpoint = id ? `/authors/${id}` : '/authors';
        try {
            const res = await API.post(endpoint, data);
            Toast.success(res.message);
            closeModal('modal-author-form');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    document.querySelectorAll('.btn-delete-author').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa tác giả này?')) return;
            try {
                const res = await API.delete(`/authors/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

window.openEditAuthorModal = function(author) {
    document.getElementById('author-id-input').value = author.id;
    document.getElementById('author-name-input').value = author.name;
    document.getElementById('author-slug-input').value = author.slug;
    document.getElementById('author-bio-input').value = author.bio || '';
    openModal('modal-author-form');
};

// ================= USER ADMIN =================
function initUserAdmin() {
    // Toggle Status
    document.querySelectorAll('.select-user-status').forEach(sel => {
        sel.addEventListener('change', async () => {
            const id = sel.dataset.id;
            const status = sel.value;
            try {
                const res = await API.post(`/admin/users/${id}`, { status });
                Toast.success(res.message);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });

    // Change Role
    document.querySelectorAll('.select-user-role').forEach(sel => {
        sel.addEventListener('change', async () => {
            const id = sel.dataset.id;
            const role_id = sel.value;
            try {
                const res = await API.post(`/admin/users/${id}`, { role_id });
                Toast.success(res.message);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });

    // Delete User
    document.querySelectorAll('.btn-delete-user').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa vĩnh viễn tài khoản người dùng này?')) return;
            try {
                const res = await API.delete(`/admin/users/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

// ================= COMMENT ADMIN =================
function initCommentAdmin() {
    document.querySelectorAll('.btn-admin-delete-comment').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
            try {
                const res = await API.delete(`/comments/${id}`);
                Toast.success(res.message);
                btn.closest('tr')?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}
