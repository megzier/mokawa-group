// Scroll-triggered animations for Mokawa Group website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initScrollAnimations();
    initCounterAnimations();
});

// Scroll-triggered animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                // Optionally unobserve after animation
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(el => observer.observe(el));
}

// Counter animation for stats
function initCounterAnimations() {
    const counters = document.querySelectorAll('.stat-number-luxury, .stat-count');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.5
    });
    
    counters.forEach(counter => counterObserver.observe(counter));
}

function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/[^0-9]/g, ''));
    
    // Handle infinity symbol
    if (element.textContent.includes('∞')) {
        return;
    }
    
    const duration = 2000; // 2 seconds
    const increment = target / (duration / 16); // 60fps
    let current = 0;
    
    element.textContent = '0';
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target + (element.dataset.suffix || '');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + (element.dataset.suffix || '');
        }
    }, 16);
}

// Add parallax effect to hero sections
document.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    parallaxElements.forEach(el => {
        const speed = el.dataset.speed || 0.5;
        el.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Add animation classes to elements on load
window.addEventListener('load', function() {
    // Hero content animation
    const heroTitle = document.querySelector('.hs-title');
    const heroSubtitle = document.querySelector('.hs-sub');
    const heroTagline = document.querySelector('.hs-tag');
    const heroButtons = document.querySelectorAll('.hs-btn-primary, .hs-btn-secondary');
    
    if (heroTitle) heroTitle.classList.add('fade-in-down');
    if (heroSubtitle) heroSubtitle.classList.add('fade-in-up', 'delay-200');
    if (heroTagline) heroTagline.classList.add('fade-in-up', 'delay-400');
    
    heroButtons.forEach((btn, index) => {
        btn.classList.add('bounce-in', `delay-${600 + (index * 100)}`);
    });
    
    // Stats cards animation
    const statCards = document.querySelectorAll('.stat-luxury-card');
    statCards.forEach((card, index) => {
        card.classList.add('animate-on-scroll', 'scale-in', `delay-${index * 100}`);
    });
    
    // Feature cards animation
    const featureCards = document.querySelectorAll('.feature-luxury-card');
    featureCards.forEach((card, index) => {
        card.classList.add('animate-on-scroll', 'fade-in-up', `delay-${index * 100}`);
    });
    
    // Gallery items animation
    const galleryItems = document.querySelectorAll('.gallery-item-luxury');
    galleryItems.forEach((item, index) => {
        item.classList.add('animate-on-scroll', 'zoom-in', `delay-${(index % 3) * 100}`);
    });
});
