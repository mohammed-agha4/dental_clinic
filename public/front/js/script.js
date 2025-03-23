function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
    document.querySelector('.sidebar-overlay').classList.toggle('show');
}

function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('show');
    document.querySelector('.sidebar-overlay').classList.remove('show');
}

// Close sidebar when window is resized to desktop view
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        closeSidebar();
    }
});

// Display the typed content
function displayContent() {
    const content = document.querySelector('textarea').value;
    document.getElementById('displayed-content').innerHTML = content;
}
