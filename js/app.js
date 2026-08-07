/**
 * Tips by Nadine - Frontend App Script
 * Handles: mobile nav, booking stepper, time slot loading,
 *          reference photo upload + preview, cancel confirmation.
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

    function baseUrl() {
        var body = document.body;
        return (body && body.getAttribute('data-base-url')) || '';
    }

    function apiUrl(path) {
        return baseUrl() + '/' + path.replace(/^\//, '');
    }

    function showToast(message, type) {
        type = type || 'info';
        var container = qs('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.setAttribute('role', 'status');
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(function () {
            toast.classList.add('hide');
            setTimeout(function () {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 250);
        }, 4000);
    }

    /* ============================================================
       Mobile Navigation
       ============================================================ */
    function initMobileNav() {
        var toggle = qs('.nav-toggle');
        var menu = qs('#nav-menu');
        if (!toggle || !menu) return;

        var lastFocus = null;

        function openMenu() {
            lastFocus = document.activeElement;
            menu.classList.add('open');
            menu.setAttribute('aria-modal', 'true');
            toggle.setAttribute('aria-expanded', 'true');
            var first = qs('.nav-mobile-link, .nav-mobile a', menu);
            if (first) first.focus();
        }

        function closeMenu() {
            menu.classList.remove('open');
            menu.removeAttribute('aria-modal');
            toggle.setAttribute('aria-expanded', 'false');
            if (lastFocus) lastFocus.focus();
        }

        toggle.addEventListener('click', function () {
            if (menu.classList.contains('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close menu when a link is clicked
        qsa('.nav-mobile-link', menu).forEach(function (link) {
            link.addEventListener('click', function () {
                closeMenu();
            });
        });

        // Escape closes the menu
        menu.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeMenu();
            }
        });

        // Keep Tab focus inside the open menu
        menu.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = qsa('a[href], button', menu);
            if (!focusable.length) return;
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });
    }

    /* ============================================================
       Booking Form - Multi-step
       ============================================================ */
    function initBookingSteps() {
        var form = qs('#booking-form');
        if (!form) return;

        var steps = qsa('.booking-step', form);
        var prevBtn = qs('#prev-btn', form);
        var submitBtn = qs('#submit-btn', form);
        var currentStep = 1;
        var totalSteps = steps.length;

        function showStep(step) {
            currentStep = step;
            steps.forEach(function (el) {
                var elStep = parseInt(el.getAttribute('data-step'), 10);
                el.hidden = elStep !== step;
            });

            prevBtn.hidden = step === 1;
            submitBtn.textContent = step === totalSteps ? 'Book Appointment' : 'Continue';
        }

        function validateStep(step) {
            var fieldset = steps.find(function (el) {
                return parseInt(el.getAttribute('data-step'), 10) === step;
            });
            if (!fieldset) return true;

            var required = qsa('input[required], select[required], textarea[required]', fieldset);
            for (var i = 0; i < required.length; i++) {
                var field = required[i];
                if (field.type === 'radio') {
                    var group = qsa('input[name="' + field.name + '"]', fieldset);
                    var checked = group.some(function (r) { return r.checked; });
                    if (!checked) {
                        field.setCustomValidity('Please select an option.');
                        field.reportValidity();
                        field.setCustomValidity('');
                        return false;
                    }
                } else if (!field.value.trim()) {
                    field.focus();
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        }

        function nextStep() {
            if (currentStep >= totalSteps) return;
            if (!validateStep(currentStep)) return;
            showStep(currentStep + 1);
            var next = steps[currentStep - 1];
            if (next) {
                var firstInput = qs('input, select, textarea', next);
                if (firstInput) firstInput.focus();
            }
        }

        function prevStep() {
            if (currentStep <= 1) return;
            showStep(currentStep - 1);
        }

        // Submit acts as "Next" until the last step
        submitBtn.addEventListener('click', function (e) {
            if (currentStep < totalSteps) {
                e.preventDefault();
                nextStep();
            }
        });

        prevBtn.addEventListener('click', prevStep);

        // Enter key advances on non-final steps (except in textareas)
        form.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && currentStep < totalSteps &&
                e.target.tagName !== 'TEXTAREA' &&
                e.target.tagName !== 'BUTTON') {
                e.preventDefault();
                nextStep();
            }
        });

        showStep(1);
    }

    /* ============================================================
       Booking Form - Time Slot Loading
       ============================================================ */
    function initTimeSlots() {
        var dateInput = qs('#booking_date');
        var slotSelect = qs('#time_slot_id');
        if (!dateInput || !slotSelect) return;

        dateInput.addEventListener('change', function () {
            var date = dateInput.value;
            if (!date) return;

            slotSelect.disabled = true;
            slotSelect.innerHTML = '<option value="">Loading available times...</option>';

            var body = new URLSearchParams();
            body.append('date', date);

            fetch(apiUrl('/ajax/booking/slots.php'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString(),
                credentials: 'same-origin'
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Request failed');
                    return response.json();
                })
                .then(function (data) {
                    slotSelect.innerHTML = '<option value="">Select a time</option>';
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    (data.slots || []).forEach(function (slot) {
                        var opt = document.createElement('option');
                        opt.value = slot.id;
                        var start = formatTime(slot.start_time);
                        var end = formatTime(slot.end_time);
                        opt.textContent = start + ' - ' + end;
                        slotSelect.appendChild(opt);
                    });
                    slotSelect.disabled = false;
                    if (data.slots && data.slots.length === 0) {
                        slotSelect.innerHTML = '<option value="">No slots available</option>';
                    }
                })
                .catch(function (err) {
                    slotSelect.innerHTML = '<option value="">Error loading slots</option>';
                    slotSelect.disabled = false;
                    showToast(err.message || 'Could not load available times.', 'error');
                });
        });
    }

    function formatTime(timeStr) {
        if (!timeStr) return '';
        var parts = timeStr.split(':');
        var h = parseInt(parts[0], 10);
        var m = parts[1] || '00';
        var suffix = h >= 12 ? 'PM' : 'AM';
        var displayH = h % 12 || 12;
        return displayH + ':' + m + ' ' + suffix;
    }

    /* ============================================================
       Reference Photo Upload + Preview
       ============================================================ */
    function initUploadArea() {
        var uploadArea = qs('#upload-area');
        var fileInput = qs('#reference_image');
        var preview = qs('#upload-preview');
        var uploadBtn = qs('#upload-btn');
        var aiBox = qs('#ai-recommendations');
        var aiCards = qs('#ai-recommendation-cards');
        if (!uploadArea || !fileInput) return;

        function handleFiles(files) {
            if (!files || !files.length) return;
            var file = files[0];
            if (!/image\/(jpeg|png|webp)/.test(file.type)) {
                showToast('Please upload a JPG, PNG, or WebP image.', 'error');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                showToast('Image must be under 5MB.', 'error');
                return;
            }

            var dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            var reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Reference photo preview';
                preview.hidden = false;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);

            if (aiBox && aiCards) {
                aiBox.hidden = true;
                aiCards.innerHTML = '';
            }
        }

        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        if (uploadBtn) {
            uploadBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                fileInput.click();
            });
        }

        fileInput.addEventListener('change', function () {
            handleFiles(fileInput.files);
        });

        // Drag & drop
        ['dragenter', 'dragover'].forEach(function (eventName) {
            uploadArea.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            uploadArea.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadArea.classList.remove('dragover');
            });
        });

        uploadArea.addEventListener('drop', function (e) {
            handleFiles(e.dataTransfer.files);
        });
    }

    /* ============================================================
       Booking Filter Tabs
       ============================================================ */
    function initBookingFilter() {
        var tabs = qsa('.filter-tab');
        var cards = qsa('.booking-card');
        if (!tabs.length || !cards.length) return;

        function applyFilter(filter) {
            var shown = 0;
            cards.forEach(function (card) {
                var state = card.getAttribute('data-state') || 'upcoming';
                var match = filter === 'all' || state === filter;
                card.hidden = !match;
                if (match) shown++;
            });
            var emptyMsg = qs('.bookings-filter-empty');
            if (emptyMsg) emptyMsg.hidden = shown > 0;
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) {
                    var active = t === tab;
                    t.classList.toggle('is-active', active);
                    t.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                applyFilter(tab.getAttribute('data-filter'));
            });
        });

        // Expose for the cancel flow so cards move between groups correctly
        window.__applyBookingFilter = applyFilter;
    }

    /* ============================================================
       Cancel Booking (inline warm confirmation)
       ============================================================ */
    function initCancelBooking() {
        qsa('.btn-cancel').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var card = btn.closest('.booking-card');
                if (!card) return;

                var panel = qs('.cancel-confirm', card);
                if (!panel) return;

                // Name the exact booking being cancelled
                var target = qs('[data-confirm-target]', panel);
                if (target && btn.getAttribute('data-when')) {
                    var service = qs('.booking-service', card);
                    var serviceName = service ? service.textContent : 'this appointment';
                    target.textContent = serviceName + ' · ' + btn.getAttribute('data-when');
                }

                panel.hidden = false;
                card.classList.add('cancelling');
                var confirmBtn = qs('.confirm-cancel', panel);
                if (confirmBtn) confirmBtn.focus();
            });
        });

        // "Keep Booking" closes the panel
        qsa('.cancel-keep').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var card = btn.closest('.booking-card');
                if (!card) return;
                var panel = qs('.cancel-confirm', card);
                if (panel) panel.hidden = true;
                card.classList.remove('cancelling');
                var cancelBtn = qs('.btn-cancel', card);
                if (cancelBtn) cancelBtn.focus();
            });
        });

        // Escape closes the open panel
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            qsa('.booking-card.cancelling').forEach(function (card) {
                var panel = qs('.cancel-confirm', card);
                if (panel) panel.hidden = true;
                card.classList.remove('cancelling');
                var cancelBtn = qs('.btn-cancel', card);
                if (cancelBtn) cancelBtn.focus();
            });
        });

        // "Yes, Cancel" submits
        qsa('.confirm-cancel').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var bookingId = btn.getAttribute('data-booking-id');
                var card = btn.closest('.booking-card');
                if (!bookingId || !card) return;

                btn.disabled = true;
                btn.textContent = 'Cancelling…';

                var body = new URLSearchParams();
                body.append('booking_id', bookingId);

                fetch(apiUrl('/ajax/booking/cancel.php'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString(),
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        // Parse the body even on HTTP errors so the real message survives
                        return response.json().then(function (data) {
                            if (!response.ok) throw new Error(data.error || 'Could not cancel this booking.');
                            return data;
                        });
                    })
                    .then(function (data) {
                        if (!data.success) throw new Error(data.error || 'Could not cancel this booking.');
                        showToast('Your appointment is cancelled. We hope to see you again soon.', 'success');
                        markCardCancelled(card);
                        if (typeof window.__applyBookingFilter === 'function') {
                            window.__applyBookingFilter(qs('.filter-tab.is-active').getAttribute('data-filter'));
                        }
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        btn.textContent = 'Yes, Cancel';
                        showToast(err.message || 'Could not cancel this booking.', 'error');
                    });
            });
        });
    }

    function markCardCancelled(card) {
        if (!card) return;

        // Status pill → Cancelled
        var pill = qs('.booking-status', card);
        if (pill) {
            pill.className = 'booking-status status-cancelled';
            pill.textContent = 'Cancelled';
        }

        // Remove cancel affordances, keep Details
        var panel = qs('.cancel-confirm', card);
        if (panel) panel.remove();
        var cancelBtn = qs('.btn-cancel', card);
        if (cancelBtn) cancelBtn.remove();

        // Move card to the cancelled group
        card.setAttribute('data-state', 'cancelled');
        card.removeAttribute('data-cancelled');
        card.classList.remove('cancelling');
        card.classList.add('is-cancelled');
    }

    /* ============================================================
       Init
       ============================================================ */
    onReady(function () {
        initMobileNav();
        initBookingSteps();
        initTimeSlots();
        initUploadArea();
        initBookingFilter();
        initCancelBooking();
    });
})();
