function toggleDropdown(element) {
    const dropdown = document.querySelector('.dropdown-content');
    const userIcon = element;
    
    if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        userIcon.classList.remove('active');
    } else {
        dropdown.classList.add('show');
        userIcon.classList.add('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown-content');
    const userIcon = document.querySelector('.user-icon');
    
    if (!event.target.closest('.user-dropdown')) {
        dropdown.classList.remove('show');
        userIcon.classList.remove('active');
    }
});
