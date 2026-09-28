// ========================================
// MOKAWA GROUP - LUXURY INTERACTIONS
// ========================================

// ========== SMOOTH SCROLL NAVIGATION ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
            
            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
        }
    });
});

// ========== NAVBAR SCROLL EFFECT ==========
let lastScroll = 0;
const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll <= 0) {
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.05)';
    } else {
        navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.1)';
    }
    
    lastScroll = currentScroll;
});

// ========== MOBILE MENU TOGGLE ==========
const menuToggle = document.querySelector('.menu-toggle');
const navbarMenu = document.querySelector('.navbar-menu');

// Inject X close button into mobile menu if not already there
if (navbarMenu && !navbarMenu.querySelector('.nav-close-btn')) {
    const closeBtn = document.createElement('button');
    closeBtn.className = 'nav-close-btn';
    closeBtn.innerHTML = '&times;';
    closeBtn.setAttribute('aria-label', 'Close menu');
    closeBtn.addEventListener('click', closeMobileMenu);
    navbarMenu.insertBefore(closeBtn, navbarMenu.firstChild);
}

function closeMobileMenu() {
    if (navbarMenu) navbarMenu.classList.remove('active');
    if (menuToggle) {
        menuToggle.classList.remove('active');
        const spans = menuToggle.querySelectorAll('span');
        spans.forEach(s => { s.style.transform = 'none'; s.style.opacity = '1'; });
    }
}

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        navbarMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
        
        // Animate menu icon
        const spans = menuToggle.querySelectorAll('span');
        if (menuToggle.classList.contains('active')) {
            spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
        } else {
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    });
}

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.navbar') && navbarMenu && navbarMenu.classList.contains('active')) {
        closeMobileMenu();
    }
});

// Close menu when any nav link is clicked (important for hotel pages)
if (navbarMenu) {
    navbarMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            closeMobileMenu();
        });
    });
}

function resetMenuIcon() {
    if (menuToggle) {
        const spans = menuToggle.querySelectorAll('span');
        spans.forEach(s => { s.style.transform = 'none'; s.style.opacity = '1'; });
    }
}

// ========== PROPERTY TABS ==========
const tabButtons = document.querySelectorAll('.tab-btn');
const propertyContents = document.querySelectorAll('.property-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const propertyId = button.getAttribute('data-property');
        
        // Remove active class from all buttons and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        propertyContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        button.classList.add('active');
        document.getElementById(propertyId).classList.add('active');
    });
});

// ========== FORM SUBMISSION ==========
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Simulate form submission
        console.log('Form submitted:', data);
        
        // Show success message
        const submitBtn = this.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Message Sent!';
        submitBtn.style.background = '#4CAF50';
        
        // Reset form
        setTimeout(() => {
            this.reset();
            submitBtn.textContent = originalText;
            submitBtn.style.background = '';
        }, 3000);
        
        // In production, you would send this to your backend:
        // fetch('/api/contact', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(data)
        // });
    });
}

// ========== SCROLL ANIMATIONS ==========
// Simple fade-in animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements with data-aos attribute
document.querySelectorAll('[data-aos]').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    
    // Add delay if specified
    const delay = el.getAttribute('data-aos-delay');
    if (delay) {
        el.style.transitionDelay = delay + 'ms';
    }
    
    observer.observe(el);
});

// ========== ACTIVE SECTION TRACKING ==========
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (window.pageYOffset >= (sectionTop - 200)) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});

// ========== COUNTER ANIMATION ==========
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const suffix = element.getAttribute('data-suffix') || '';
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target + suffix;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + suffix;
        }
    }, 16);
}

// Trigger counter animation when in viewport
const statNumbers = document.querySelectorAll('.stat-number');
let countersAnimated = false;

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !countersAnimated) {
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target') || stat.textContent);
                if (!isNaN(target)) {
                    animateCounter(stat, target);
                }
            });
            countersAnimated = true;
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats-row');
if (statsSection) {
    statsObserver.observe(statsSection);
}

// ========== HERO SLIDER (if multiple slides added) ==========
const heroSlides = document.querySelectorAll('.hero-slide');
let currentSlide = 0;

function nextSlide() {
    if (heroSlides.length > 1) {
        heroSlides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % heroSlides.length;
        heroSlides[currentSlide].classList.add('active');
    }
}

// Auto-advance slides every 7 seconds
if (heroSlides.length > 1) {
    setInterval(nextSlide, 7000);
}

// ========== PRELOADER (Optional) ==========
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
    
    // Hero entrance handled by CSS animations
});

// ========== PARALLAX EFFECT (Subtle) ==========
// Disabled - hero image animation handled via CSS

// ========== SMOOTH REVEAL FOR IMAGES ==========
// Skipped - images styled via CSS to avoid FOUC on hero

// ========== RESPONSIVE BEHAVIOR ==========
function handleResize() {
    const width = window.innerWidth;
    
    // Close mobile menu on resize to desktop
    if (width > 768 && navbarMenu && navbarMenu.classList.contains('active')) {
        closeMobileMenu();
    }
}

window.addEventListener('resize', handleResize);

// ========== CONSOLE GREETING ==========
console.log('%c Mokawa Group ', 'background: #C9A961; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold;');
console.log('%c Rooted in Culture, Designed to Deliver ', 'color: #666; font-style: italic; font-size: 12px;');

// ========== ENHANCED NAVIGATION SCROLL ========== 
// Add background IMMEDIATELY when scrolling past hero
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    
    // Add 'scrolled' class as soon as user scrolls ANY amount
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Also trigger on page load in case user refreshes mid-page
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    }
});

/* ============================================================
   HERO V2 — AUTO-ADVANCING SLIDER + COUNTER ANIMATION
   ============================================================ */
(function() {

    // ---- SLIDER ----
    var slides = document.querySelectorAll('.hv2-slide');
    var dots   = document.querySelectorAll('.hv2-dot');
    var current = 0;
    var timer;

    function hv2GoTo(n) {
        slides[current].classList.remove('active');
        slides[current].classList.add('leaving');
        dots[current].classList.remove('active');

        var prev = current;
        current = (n + slides.length) % slides.length;

        slides[current].classList.add('active');
        dots[current].classList.add('active');

        setTimeout(function() {
            slides[prev].classList.remove('leaving');
        }, 1400);

        clearInterval(timer);
        timer = setInterval(advance, 6000);
    }

    function advance() {
        hv2GoTo(current + 1);
    }

    // Expose for inline onclick
    window.hv2GoTo = hv2GoTo;

    if (slides.length > 0) {
        timer = setInterval(advance, 6000);
    }

    // ---- COUNTER ANIMATION ----
    function animateCounters() {
        document.querySelectorAll('.hv2-stat-n[data-target]').forEach(function(el) {
            var target  = parseInt(el.getAttribute('data-target'), 10);
            var suffix  = el.getAttribute('data-suffix') || '';
            var start   = 0;
            var dur     = 1600;
            var step    = dur / target;
            var t0      = null;

            function tick(ts) {
                if (!t0) t0 = ts;
                var progress = Math.min((ts - t0) / dur, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target) + suffix;
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target + suffix;
            }
            requestAnimationFrame(tick);
        });
    }

    // Trigger counters when stats enter viewport
    var statsEl = document.querySelector('.hv2-stats');
    if (statsEl && window.IntersectionObserver) {
        var obs = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                setTimeout(animateCounters, 1600);
                obs.disconnect();
            }
        }, { threshold: 0.5 });
        obs.observe(statsEl);
    } else if (statsEl) {
        setTimeout(animateCounters, 1800);
    }

})();
