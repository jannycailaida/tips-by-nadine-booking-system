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

        toggle.addEventListener('click', function () {
            var open = menu.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        // Close menu when a link is clicked
        qsa('.nav-mobile-link', menu).forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            });
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
       Cancel Booking Confirmation
       ============================================================ */
    function initCancelBooking() {
        qsa('.btn-cancel').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var bookingId = btn.getAttribute('data-booking-id');
                if (!bookingId) return;

                var confirmed = window.confirm('Are you sure you want to cancel this booking? This cannot be undone.');
                if (!confirmed) return;

                btn.disabled = true;
                btn.textContent = 'Cancelling...';

                var body = new URLSearchParams();
                body.append('booking_id', bookingId);

                fetch(apiUrl('/ajax/booking/cancel.php'), {
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
                        if (data.success) {
                            showToast('Booking cancelled.', 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 800);
                        } else {
                            throw new Error(data.error || 'Could not cancel booking.');
                        }
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        btn.textContent = 'Cancel';
                        showToast(err.message || 'Could not cancel booking.', 'error');
                    });
            });
        });
    }

    /* ============================================================
       Init
       ============================================================ */
    onReady(function () {
        initMobileNav();
        initBookingSteps();
        initTimeSlots();
        initUploadArea();
        initCancelBooking();
    });
})();
