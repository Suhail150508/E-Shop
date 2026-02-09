/**
 * Gallery drag-and-drop upload
 * Reusable script for admin product create/edit and any form with gallery_images[].
 * Expects: #galleryDropZone, #gallery_images (file input), #galleryPreviewList
 * Optional on zone: data-remove-title (tooltip for remove button)
 */
(function() {
    'use strict';

    function init() {
        var zone = document.getElementById('galleryDropZone');
        var input = document.getElementById('gallery_images');
        var previewList = document.getElementById('galleryPreviewList');
        if (!zone || !input || !previewList) return;

        var removeTitle = (zone.getAttribute('data-remove-title') || 'Remove').replace(/&quot;/g, '"');

        function setInputFiles(files) {
            var dt = new DataTransfer();
            for (var i = 0; i < files.length; i++) {
                dt.items.add(files[i]);
            }
            input.files = dt.files;
        }

        function renderPreviews() {
            previewList.innerHTML = '';
            var files = input.files;
            for (var i = 0; i < files.length; i++) {
                (function(idx) {
                    var file = files[idx];
                    if (!file.type || !file.type.match(/^image\/(jpeg|png|jpg|webp)$/i)) return;
                    var div = document.createElement('div');
                    div.className = 'gallery-preview-item';
                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = '';
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'gallery-preview-remove';
                    btn.innerHTML = '&times;';
                    btn.title = removeTitle;
                    btn.setAttribute('aria-label', removeTitle);
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var newFiles = [];
                        for (var j = 0; j < input.files.length; j++) {
                            if (j !== idx) newFiles.push(input.files[j]);
                        }
                        setInputFiles(newFiles);
                        renderPreviews();
                    });
                    div.appendChild(img);
                    div.appendChild(btn);
                    previewList.appendChild(div);
                })(i);
            }
        }

        function triggerInput() {
            input.click();
        }

        zone.addEventListener('click', function(e) {
            if (e.target === zone || (e.target.closest && e.target.closest('.gallery-drop-zone-inner'))) {
                triggerInput();
            }
        });
        zone.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                triggerInput();
            }
        });
        input.addEventListener('change', renderPreviews);

        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            zone.classList.remove('drag-over');
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('drag-over');
            var newFiles = e.dataTransfer && e.dataTransfer.files;
            if (!newFiles || !newFiles.length) return;
            var existing = [];
            for (var i = 0; i < input.files.length; i++) {
                existing.push(input.files[i]);
            }
            for (var j = 0; j < newFiles.length; j++) {
                if (newFiles[j].type && newFiles[j].type.match(/^image\/(jpeg|png|jpg|webp)$/i)) {
                    existing.push(newFiles[j]);
                }
            }
            setInputFiles(existing);
            renderPreviews();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
