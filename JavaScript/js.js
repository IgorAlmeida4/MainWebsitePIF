function scaleElement(el, scale, duration) {
    el.style.transition = `transform ${duration}ms ease`;
    el.style.transform = `scale(${scale})`;
}

// Select all navbar items and the logo
const navbarItems = document.querySelectorAll('.navbar-item');
const logo = document.querySelector('#navbar-logo'); // Select logo separately

// Add event listeners for navbar items
navbarItems.forEach(item => {
    item.addEventListener('mouseover', () => {
        scaleElement(item, 1.2, 300);
    });

    item.addEventListener('mouseout', () => {
        scaleElement(item, 1, 300);
    });
});

// Add event listeners for the logo
if (logo) {
    logo.addEventListener('mouseover', () => {
        scaleElement(logo, 1.2, 300);
    });

    logo.addEventListener('mouseout', () => {
        scaleElement(logo, 1, 300);
    });
}
