/**
 * Global Insights - Main JavaScript
 */

// Like Article Function
function likeArticle(articleId) {
    const button = document.getElementById('like-button');
    const countSpan = document.getElementById('like-count');
    
    // Disable button during request
    button.disabled = true;
    
    fetch('/public/api/like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `article_id=${articleId}&csrf_token=${getCsrfToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            countSpan.textContent = data.like_count;
            button.classList.add('liked');
            button.querySelector('.like-text').textContent = 'Liked';
        } else {
            alert(data.message || 'Failed to like article');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Copy Link Function
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const copyBtn = document.querySelector('.share-btn-copy');
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Copied!';
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy link');
    });
}

// Get CSRF Token from meta tag or form
function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    return tokenInput ? tokenInput.value : '';
}

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    // Comment Form Validation
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            const contentField = document.getElementById('comment-content');
            const content = contentField.value.trim();
            
            if (content.length < 10) {
                e.preventDefault();
                alert('Comment must be at least 10 characters long');
                contentField.focus();
                return false;
            }
            
            if (content.length > 1000) {
                e.preventDefault();
                alert('Comment must not exceed 1000 characters');
                contentField.focus();
                return false;
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });
    
    // Lazy load images (for browsers that don't support native lazy loading)
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src || img.src;
        });
    } else {
        // Fallback for older browsers
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
        document.body.appendChild(script);
    }
});

// Smooth scroll to comments
function scrollToComments() {
    const commentsSection = document.getElementById('comments-section');
    if (commentsSection) {
        commentsSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Character counter for comment form
const commentTextarea = document.getElementById('comment-content');
if (commentTextarea) {
    const maxLength = 1000;
    const counter = document.createElement('small');
    counter.className = 'text-muted float-end';
    counter.id = 'char-counter';
    commentTextarea.parentElement.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - commentTextarea.value.length;
        counter.textContent = `${remaining} characters remaining`;
        if (remaining < 100) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    }
    
    commentTextarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Search functionality
const searchForm = document.getElementById('search-form');
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        const searchInput = document.getElementById('search-query');
        const query = searchInput.value.trim();
        
        if (query.length < 3) {
            e.preventDefault();
            alert('Please enter at least 3 characters to search');
            searchInput.focus();
            return false;
        }
    });
}

// Admin: Rich Text Editor initialization (if TinyMCE is loaded)
if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: '.rich-editor',
        height: 500,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
        menubar: 'file edit view insert format tools table help',
        branding: false,
        relative_urls: false,
        remove_script_host: false,
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        image_advtab: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '/admin/upload.php',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
}

// Delete confirmation
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Print article
function printArticle() {
    window.print();
}

// Share on social media
function shareOnSocial(platform, url, title) {
    const encodedUrl = encodeURIComponent(url);
    const encodedTitle = encodeURIComponent(title);
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
            break;
        case 'whatsapp':
            shareUrl = `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}


document.addEventListener("DOMContentLoaded", function() {
    const flash = document.querySelector('.flash-message');
    if (flash) {
        setTimeout(() => flash.style.display = 'none', 5000);
    }
});
