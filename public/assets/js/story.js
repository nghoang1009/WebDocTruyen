/**
 * WebDocTruyen - Story Details & Interactions
 * Rating, Comments, Follow, Favorite, Chapters Filter
 */

document.addEventListener('DOMContentLoaded', () => {
    initStoryDetail();
});

function initStoryDetail() {
    const storyId = window.STORY_ID;
    if (!storyId) return;

    initFollowAndFavorite(storyId);
    initRatingWidget(storyId);
    initComments(storyId);
    initChapterListFilter();
}

// ================= FOLLOW & FAVORITE =================
function initFollowAndFavorite(storyId) {
    const followBtn = document.getElementById('btn-toggle-follow');
    const favBtn = document.getElementById('btn-toggle-favorite');

    if (followBtn) {
        followBtn.addEventListener('click', async () => {
            if (!window.IS_LOGGED_IN) {
                Toast.error('Vui lòng đăng nhập để theo dõi truyện.');
                return;
            }
            try {
                const res = await API.post(`/stories/${storyId}/follow`);
                const isFollowing = res.data.is_following;
                followBtn.classList.toggle('btn-primary', !isFollowing);
                followBtn.classList.toggle('btn-outline', isFollowing);
                followBtn.querySelector('.btn-text').innerText = isFollowing ? 'Đang theo dõi' : 'Theo dõi';
                Toast.success(res.message);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    }

    if (favBtn) {
        favBtn.addEventListener('click', async () => {
            if (!window.IS_LOGGED_IN) {
                Toast.error('Vui lòng đăng nhập để yêu thích truyện.');
                return;
            }
            try {
                const res = await API.post(`/stories/${storyId}/favorite`);
                const isFav = res.data.is_favorited;
                favBtn.classList.toggle('btn-accent', isFav);
                favBtn.classList.toggle('btn-outline', !isFav);
                favBtn.querySelector('.btn-text').innerText = isFav ? 'Đã thích' : 'Yêu thích';
                Toast.success(res.message);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    }
}

// ================= RATING 5 STARS WIDGET =================
function initRatingWidget(storyId) {
    const starBtns = document.querySelectorAll('.star-rating-btn');
    if (!starBtns.length) return;

    starBtns.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            const val = parseInt(btn.dataset.value, 10);
            highlightStars(val);
        });

        btn.addEventListener('click', async () => {
            if (!window.IS_LOGGED_IN) {
                Toast.error('Vui lòng đăng nhập để đánh giá truyện.');
                return;
            }
            const val = parseInt(btn.dataset.value, 10);
            try {
                const res = await API.post(`/stories/${storyId}/rating`, { rating: val });
                Toast.success('Cảm ơn bạn đã đánh giá ' + val + ' sao!');
                document.getElementById('story-rating-avg').innerText = res.data.rating_avg.toFixed(1);
                document.getElementById('story-rating-count').innerText = res.data.rating_count;
                highlightStars(val, true);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });

    const ratingContainer = document.getElementById('star-rating-container');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', () => {
            const currentVal = parseInt(ratingContainer.dataset.userRating || '0', 10);
            highlightStars(currentVal);
        });
    }
}

function highlightStars(count, persist = false) {
    const starBtns = document.querySelectorAll('.star-rating-btn');
    starBtns.forEach(btn => {
        const val = parseInt(btn.dataset.value, 10);
        btn.style.color = val <= count ? '#f59e0b' : '#64748b';
    });
    if (persist) {
        const container = document.getElementById('star-rating-container');
        if (container) container.dataset.userRating = count;
    }
}

// ================= COMMENTS SYSTEM =================
function initComments(storyId) {
    const form = document.getElementById('comment-form');
    const commentInput = document.getElementById('comment-input');
    const commentList = document.getElementById('comments-list-container');

    if (!form || !commentList) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!window.IS_LOGGED_IN) {
            Toast.error('Vui lòng đăng nhập để bình luận.');
            return;
        }

        const content = commentInput.value.trim();
        if (!content) return;

        try {
            const res = await API.post(`/stories/${storyId}/comments`, { content });
            Toast.success('Bình luận thành công!');
            commentInput.value = '';
            // Prepend new comment to list
            loadComments(storyId);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    // Load initial comments
    loadComments(storyId);
}

async function loadComments(storyId, page = 1) {
    const container = document.getElementById('comments-list-container');
    if (!container) return;

    try {
        const res = await API.get(`/stories/${storyId}/comments`, { page, limit: 15 });
        if (!res.data || res.data.length === 0) {
            container.innerHTML = `<div style="text-align:center;padding:30px;color:var(--text-muted);">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nghĩ!</div>`;
            return;
        }

        container.innerHTML = res.data.map(comment => renderCommentHTML(comment)).join('');
        attachCommentEventListeners(storyId);
    } catch (err) {
        console.error(err);
    }
}

function renderCommentHTML(comment) {
    const isOwner = window.CURRENT_USER_ID && (window.CURRENT_USER_ID == comment.user_id || window.IS_ADMIN);
    const repliesHtml = (comment.replies || []).map(r => `
        <div class="comment-reply-item" style="margin-left:48px; margin-top:12px; padding:12px; background:var(--bg-tertiary); border-radius:var(--radius-md);" id="comment-${r.id}">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <div style="font-weight:700; font-size:0.85rem; color:var(--text-primary); display:flex; align-items:center; gap:6px;">
                    ${r.username} ${r.role_id == 1 ? '<span style="font-size:0.7rem; background:var(--primary); color:#fff; padding:1px 6px; border-radius:4px;">ADMIN</span>' : ''}
                </div>
                <span style="font-size:0.75rem; color:var(--text-muted);">${r.created_at}</span>
            </div>
            <p style="font-size:0.88rem; color:var(--text-secondary); line-height:1.5;">${escapeHtml(r.content)}</p>
            ${(window.CURRENT_USER_ID && (window.CURRENT_USER_ID == r.user_id || window.IS_ADMIN)) ? `
                <button class="btn-delete-comment" data-id="${r.id}" style="background:none; border:none; color:var(--danger); font-size:0.75rem; cursor:pointer; margin-top:6px;">Xóa</button>
            ` : ''}
        </div>
    `).join('');

    return `
        <div class="comment-item" style="padding:16px 0; border-bottom:1px solid var(--border-color);" id="comment-${comment.id}">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        ${comment.username.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:0.92rem; color:var(--text-primary); display:flex; align-items:center; gap:6px;">
                            ${comment.username}
                            ${comment.role_id == 1 ? '<span style="font-size:0.7rem; background:var(--primary); color:#fff; padding:1px 6px; border-radius:4px;">ADMIN</span>' : ''}
                        </div>
                        <div style="font-size:0.78rem; color:var(--text-muted);">${comment.created_at}</div>
                    </div>
                </div>
                ${isOwner ? `
                    <button class="btn-delete-comment" data-id="${comment.id}" style="background:none; border:none; color:var(--danger); font-size:0.8rem; cursor:pointer;">Xóa</button>
                ` : ''}
            </div>
            <p style="font-size:0.92rem; color:var(--text-secondary); line-height:1.6; margin-bottom:8px;">${escapeHtml(comment.content)}</p>
            <button class="btn-reply-toggle" data-id="${comment.id}" style="background:none; border:none; color:var(--primary); font-size:0.82rem; font-weight:600; cursor:pointer;">Trả lời</button>

            <!-- Reply Box Form Hidden by Default -->
            <div class="reply-box-form" id="reply-box-${comment.id}" style="display:none; margin-top:10px; margin-left:48px;">
                <textarea class="form-control" placeholder="Viết phản hồi của bạn..." rows="2" style="font-size:0.85rem; margin-bottom:6px;"></textarea>
                <div style="display:flex; gap:8px;">
                    <button class="btn btn-sm btn-primary btn-submit-reply" data-parent-id="${comment.id}">Gửi phản hồi</button>
                    <button class="btn btn-sm btn-outline btn-cancel-reply" data-id="${comment.id}">Hủy</button>
                </div>
            </div>

            <div class="replies-list">${repliesHtml}</div>
        </div>
    `;
}

function attachCommentEventListeners(storyId) {
    // Reply toggle
    document.querySelectorAll('.btn-reply-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!window.IS_LOGGED_IN) {
                Toast.error('Vui lòng đăng nhập để phản hồi.');
                return;
            }
            const id = btn.dataset.id;
            const box = document.getElementById(`reply-box-${id}`);
            if (box) box.style.display = box.style.display === 'none' ? 'block' : 'none';
        });
    });

    document.querySelectorAll('.btn-cancel-reply').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const box = document.getElementById(`reply-box-${id}`);
            if (box) box.style.display = 'none';
        });
    });

    // Submit reply
    document.querySelectorAll('.btn-submit-reply').forEach(btn => {
        btn.addEventListener('click', async () => {
            const parentId = btn.dataset.parentId;
            const box = document.getElementById(`reply-box-${parentId}`);
            const input = box.querySelector('textarea');
            const content = input.value.trim();
            if (!content) return;

            try {
                await API.post(`/stories/${storyId}/comments`, {
                    parent_id: parentId,
                    content
                });
                Toast.success('Phản hồi thành công!');
                loadComments(storyId);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });

    // Delete comment
    document.querySelectorAll('.btn-delete-comment').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) return;
            const id = btn.dataset.id;
            try {
                await API.delete(`/comments/${id}`);
                Toast.success('Đã xóa bình luận.');
                loadComments(storyId);
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });
}

// ================= CHAPTER LIST FILTER =================
function initChapterListFilter() {
    const searchInput = document.getElementById('chapter-search-filter');
    const chapterItems = document.querySelectorAll('.chapter-list-item');

    if (searchInput && chapterItems.length > 0) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase().trim();
            chapterItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? 'flex' : 'none';
            });
        });
    }

    // Sort order ASC / DESC toggle
    const sortBtn = document.getElementById('btn-sort-chapters');
    const chaptersContainer = document.getElementById('chapters-grid-container');
    if (sortBtn && chaptersContainer) {
        let isAsc = true;
        sortBtn.addEventListener('click', () => {
            isAsc = !isAsc;
            sortBtn.innerText = isAsc ? 'Cũ nhất trước' : 'Mới nhất trước';
            const items = Array.from(chaptersContainer.children);
            items.reverse();
            chaptersContainer.innerHTML = '';
            items.forEach(el => chaptersContainer.appendChild(el));
        });
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
