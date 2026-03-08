
// Page navigation
function navigateToPage(pageId) {
    document.querySelectorAll('.page').forEach(page => {
        page.style.display = 'none';
        page.classList.remove('active');
    });
    const target = document.getElementById(pageId);
    if (target) {
        target.style.display = 'block';
        target.classList.add('active');
    }
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === pageId) link.classList.add('active');
    });
}




// Initialize nav links
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            navigateToPage(link.dataset.page);
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.page').forEach(page => {
        if (page.id !== 'home') {
            page.style.display = 'none';
        }
    });
});