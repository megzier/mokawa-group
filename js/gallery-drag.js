/**
 * Drag-to-scroll horizontal gallery + lightbox integration
 * All gallery items open in lightbox, not new tabs
 */
(function() {
    function initScrollGalleries() {
        // Enable drag scroll on all .gal-scroll-wrap elements
        document.querySelectorAll('.gal-scroll-wrap').forEach(function(wrap) {
            var isDown = false;
            var startX, scrollLeft;

            wrap.addEventListener('mousedown', function(e) {
                isDown = true;
                wrap.classList.add('active');
                startX = e.pageX - wrap.offsetLeft;
                scrollLeft = wrap.scrollLeft;
            });

            wrap.addEventListener('mouseleave', function() {
                isDown = false;
                wrap.classList.remove('active');
            });

            wrap.addEventListener('mouseup', function() {
                isDown = false;
                wrap.classList.remove('active');
            });

            wrap.addEventListener('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();
                var x = e.pageX - wrap.offsetLeft;
                var walk = (x - startX) * 1.5;
                wrap.scrollLeft = scrollLeft - walk;
            });

            // Touch support
            var touchStartX = 0;
            var touchScrollLeft = 0;
            wrap.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].pageX;
                touchScrollLeft = wrap.scrollLeft;
            }, { passive: true });

            wrap.addEventListener('touchmove', function(e) {
                var touchX = e.touches[0].pageX;
                var walk = (touchStartX - touchX) * 1.2;
                wrap.scrollLeft = touchScrollLeft + walk;
            }, { passive: true });
        });

        // Attach lightbox to all gallery items (including scroll galleries)
        document.querySelectorAll('.gal-scroll .gal-item').forEach(function(item, idx) {
            var wrap = item.closest('.gal-scroll');
            if (!wrap) return;

            item.addEventListener('click', function(e) {
                e.preventDefault();
                // Build array of all image hrefs in this scroll gallery
                var allItems = Array.from(wrap.querySelectorAll('.gal-item'));
                var images = allItems.map(function(a) { return a.href || a.getAttribute('href'); });
                var clickedIndex = allItems.indexOf(item);

                if (typeof openLightbox === 'function') {
                    // Override gallery images array and open
                    if (typeof galleryImages !== 'undefined') {
                        galleryImages = images;
                        openLightbox(clickedIndex);
                    } else {
                        openLightbox(clickedIndex);
                    }
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollGalleries);
    } else {
        initScrollGalleries();
    }
})();
