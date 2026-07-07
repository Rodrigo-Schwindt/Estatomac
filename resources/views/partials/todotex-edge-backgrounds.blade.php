<style>
    [data-edge-bg-target] {
        background-color: #f7f7f7;
        transition: background-color 300ms ease;
    }
</style>

<script>
(() => {
    if (window.todotexEdgeBackgroundsReady) {
        window.applyTodotexEdgeBackgrounds?.();
        return;
    }

    window.todotexEdgeBackgroundsReady = true;

    const cache = new Map();
    const fallbackColor = 'rgb(247, 247, 247)';

    function softenedChannel(value) {
        return Math.round((value * 0.28) + (255 * 0.72));
    }

    function softColorFromAverage(red, green, blue) {
        return `rgb(${softenedChannel(red)}, ${softenedChannel(green)}, ${softenedChannel(blue)})`;
    }

    function readEdgeColor(img) {
        if (!img.naturalWidth || !img.naturalHeight) {
            return null;
        }

        const maxSize = 72;
        const scale = Math.min(1, maxSize / Math.max(img.naturalWidth, img.naturalHeight));
        const width = Math.max(1, Math.round(img.naturalWidth * scale));
        const height = Math.max(1, Math.round(img.naturalHeight * scale));
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });

        canvas.width = width;
        canvas.height = height;
        context.drawImage(img, 0, 0, width, height);

        const pixels = context.getImageData(0, 0, width, height).data;
        const edgeDepth = Math.max(2, Math.round(Math.min(width, height) * 0.12));
        let red = 0;
        let green = 0;
        let blue = 0;
        let count = 0;

        function addPixel(x, y) {
            const offset = ((y * width) + x) * 4;
            const alpha = pixels[offset + 3] / 255;

            if (alpha < 0.15) {
                return;
            }

            red += (pixels[offset] * alpha) + (255 * (1 - alpha));
            green += (pixels[offset + 1] * alpha) + (255 * (1 - alpha));
            blue += (pixels[offset + 2] * alpha) + (255 * (1 - alpha));
            count += 1;
        }

        for (let y = 0; y < height; y += 1) {
            for (let x = 0; x < width; x += 1) {
                if (x < edgeDepth || x >= width - edgeDepth || y < edgeDepth || y >= height - edgeDepth) {
                    addPixel(x, y);
                }
            }
        }

        if (count < 12) {
            for (let y = 0; y < height; y += 1) {
                for (let x = 0; x < width; x += 1) {
                    addPixel(x, y);
                }
            }
        }

        return count
            ? softColorFromAverage(red / count, green / count, blue / count)
            : fallbackColor;
    }

    function applyEdgeBackground(img) {
        const target = img.closest('[data-edge-bg-target]') || img.parentElement;
        const src = img.currentSrc || img.src;

        if (!target || !src) {
            return;
        }

        if (!img.complete || !img.naturalWidth) {
            return;
        }

        if (cache.has(src)) {
            target.style.backgroundColor = cache.get(src);
            return;
        }

        try {
            const color = readEdgeColor(img) || fallbackColor;
            cache.set(src, color);
            target.style.backgroundColor = color;
        } catch (error) {
            target.style.backgroundColor = fallbackColor;
        }
    }

    function bindImage(img) {
        if (img.dataset.edgeBgBound === 'true') {
            return;
        }

        img.dataset.edgeBgBound = 'true';
        img.addEventListener('load', () => applyEdgeBackground(img));

        if (img.complete) {
            applyEdgeBackground(img);
        }
    }

    window.applyTodotexEdgeBackgrounds = function applyTodotexEdgeBackgrounds(root = document) {
        root.querySelectorAll?.('img[data-edge-bg-image]').forEach(bindImage);
    };

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.target.matches?.('img[data-edge-bg-image]')) {
                applyEdgeBackground(mutation.target);
                return;
            }

            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return;
                }

                if (node.matches?.('img[data-edge-bg-image]')) {
                    bindImage(node);
                }

                node.querySelectorAll?.('img[data-edge-bg-image]').forEach(bindImage);
            });
        });
    });

    window.applyTodotexEdgeBackgrounds();
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['src']
    });

    document.addEventListener('livewire:navigated', () => window.applyTodotexEdgeBackgrounds());
    document.addEventListener('livewire:updated', () => window.applyTodotexEdgeBackgrounds());
})();
</script>
