/**
 * Tips by Nadine - Admin Script
 * Handles: sidebar toggle, design modal (add/edit), image preview.
 */
(function () {
    'use strict';

    /* ============================================================
       Utilities
       ============================================================ */
    function qs(selector, scope) {
        return (scope || document).querySelector(selector);
    }

    function qsa(selector, scope) {
        return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
    }

    function onReady(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    /* ============================================================
       Sidebar Toggle
       ============================================================ */
    function initSidebar() {
        var toggle = qs('#admin-sidebar-toggle');
        var sidebar = qs('#admin-sidebar');
        var overlay = qs('#admin-overlay');
        if (!toggle || !sidebar) return;

        function closeSidebar() {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('visible');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function () {
            var open = sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('visible', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        qsa('.admin-sidebar-link', sidebar).forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
    }

    /* ============================================================
       Design Modal (Add / Edit)
       ============================================================ */
    function initDesignModal() {
        var modal = qs('#design-modal');
        if (!modal) return;

        var addBtn = qs('#add-design-btn');
        var editBtns = qsa('.edit-design-btn');
        var closeButtons = qsa('.modal-close, .modal-cancel', modal);
        var overlay = qs('.modal-overlay', modal);
        var title = qs('#modal-title', modal);
        var form = qs('#design-form', modal);
        var designIdInput = qs('#design_id', modal);

        var fields = {
            name: qs('#design_name', modal),
            category_id: qs('#design_category', modal),
            price: qs('#design_price', modal),
            description: qs('#design_description', modal)
        };

        function openModal(mode, design) {
            // mode: 'add' | 'edit'
            form.reset();
            if (designIdInput) designIdInput.value = '';
            resetPreview();

            if (mode === 'edit' && design) {
                if (title) title.textContent = 'Edit Design';
                if (designIdInput) designIdInput.value = design.id;
                if (fields.name) fields.name.value = design.name || '';
                if (fields.category_id) fields.category_id.value = design.category_id || '';
                if (fields.price) fields.price.value = design.price || '';
                if (fields.description) fields.description.value = design.description || '';
            } else {
                if (title) title.textContent = 'Add New Design';
            }

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            if (fields.name) {
                setTimeout(function () { fields.name.focus(); }, 50);
            }
        }

        function closeModal() {
            modal.hidden = true;
            document.body.style.overflow = '';
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                openModal('add');
            });
        }

        editBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                try {
                    var design = JSON.parse(btn.getAttribute('data-design') || '{}');
                    openModal('edit', design);
                } catch (e) {
                    // Ignore malformed data; fall back to add modal
                    openModal('add');
                }
            });
        });

        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', closeModal);
        });

        if (overlay) {
            overlay.addEventListener('click', closeModal);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) closeModal();
        });
    }

    /* ============================================================
       Design Image Preview (in modal)
       ============================================================ */
    function initDesignImagePreview() {
        var fileInput = qs('#design_image');
        var preview = qs('#image-preview');
        if (!fileInput || !preview) return;

        fileInput.addEventListener('change', function () {
            var file = fileInput.files && fileInput.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Design image preview';
                preview.hidden = false;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    function resetPreview() {
        var preview = qs('#image-preview');
        var fileInput = qs('#design_image');
        if (preview) {
            preview.innerHTML = '';
            preview.hidden = true;
        }
        if (fileInput) fileInput.value = '';
    }

    /* ============================================================
       Init
       ============================================================ */
    onReady(function () {
        initSidebar();
        initDesignModal();
        initDesignImagePreview();
    });
})();
