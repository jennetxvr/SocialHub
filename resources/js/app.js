// Global SocialHub Interactive Helpers

window.toggleLike = async function (postId, button) {
    const icon = button.querySelector('svg');
    const countSpan = button.querySelector('.like-count');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Optimistic UI feedback
    const wasLiked = icon.classList.contains('fill-current');
    let currentCount = parseInt(countSpan.textContent, 10) || 0;

    if (wasLiked) {
        icon.classList.remove('fill-current', 'text-pink-600');
        icon.classList.add('fill-none');
        button.classList.remove('text-pink-600');
        countSpan.textContent = Math.max(0, currentCount - 1);
    } else {
        icon.classList.add('fill-current', 'text-pink-600');
        icon.classList.remove('fill-none');
        button.classList.add('text-pink-600');
        countSpan.textContent = currentCount + 1;
    }

    try {
        const response = await fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to toggle like');
        }

        const data = await response.json();
        countSpan.textContent = data.likes_count;

        if (data.liked) {
            icon.classList.add('fill-current', 'text-pink-600');
            icon.classList.remove('fill-none');
            button.classList.add('text-pink-600');
        } else {
            icon.classList.remove('fill-current', 'text-pink-600');
            icon.classList.add('fill-none');
            button.classList.remove('text-pink-600');
        }
    } catch (err) {
        // Rollback optimistic update on error
        countSpan.textContent = currentCount;
        if (wasLiked) {
            icon.classList.add('fill-current', 'text-pink-600');
            button.classList.add('text-pink-600');
        } else {
            icon.classList.remove('fill-current', 'text-pink-600');
            button.classList.remove('text-pink-600');
        }
    }
};

window.toggleFollow = async function (userId, button) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch(`/users/${userId}/follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.following) {
                button.textContent = 'Following';
                button.classList.remove('bg-indigo-600', 'text-white');
                button.classList.add('bg-slate-100', 'text-slate-800', 'hover:bg-slate-200');
            } else {
                button.textContent = 'Follow';
                button.classList.add('bg-indigo-600', 'text-white');
                button.classList.remove('bg-slate-100', 'text-slate-800', 'hover:bg-slate-200');
            }
        }
    } catch (e) {}
};
