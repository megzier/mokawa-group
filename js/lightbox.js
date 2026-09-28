// Lightbox Gallery Functionality
let currentImageIndex = 0;
let galleryImages = [];

document.addEventListener('DOMContentLoaded', function() {
    // Create lightbox HTML if not present
    if (!document.getElementById('lightbox')) {
        document.body.insertAdjacentHTML('beforeend', `
            <div id="lightbox" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
                <span class="lightbox-prev" onclick="changeImage(-1)">&#10094;</span>
                <img class="lightbox-content" id="lightbox-img" alt="Gallery Image">
                <span class="lightbox-next" onclick="changeImage(1)">&#10095;</span>
            </div>
        `);
    }

    // Collect gallery images from both old .gallery-item-luxury and new .gal-item
    const galleryLinks = document.querySelectorAll('.gallery-item-luxury, .gal-item, .gal-scroll .gal-item');
    galleryImages = Array.from(galleryLinks).map(link => link.getAttribute('href'));

    // Remove duplicate hrefs (from querySelectorAll overlap)
    const seen = new Set();
    const uniqueLinks = [];
    galleryLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!seen.has(href)) {
            seen.add(href);
            uniqueLinks.push(link);
        }
    });
    galleryImages = uniqueLinks.map(link => link.getAttribute('href'));

    // Attach click handlers to all gallery links
    uniqueLinks.forEach((link, index) => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openLightbox(index);
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const lb = document.getElementById('lightbox');
        if (lb && lb.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') changeImage(-1);
            if (e.key === 'ArrowRight') changeImage(1);
        }
    });

    // Click outside image to close
    document.getElementById('lightbox')?.addEventListener('click', function(e) {
        if (e.target.id === 'lightbox') closeLightbox();
    });
});

function openLightbox(index) {
    currentImageIndex = index;
    const lb = document.getElementById('lightbox');
    const img = document.getElementById('lightbox-img');
    img.src = galleryImages[currentImageIndex];
    lb.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
    const img = document.getElementById('lightbox-img');
    img.style.opacity = '0';
    setTimeout(() => {
        img.src = galleryImages[currentImageIndex];
        img.style.opacity = '1';
    }, 150);
}
