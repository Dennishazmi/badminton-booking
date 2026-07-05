// Daiman Sports Complex — Main JS

document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(a => {
            a.style.opacity = '0';
            a.style.transition = 'opacity 0.5s';
            setTimeout(() => a.remove(), 500);
        });
    }, 5000);

    // Booking — court selection
    document.querySelectorAll('.court-select-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.court-select-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const courtId = this.dataset.courtId;
            const courtName = this.dataset.courtName;
            const courtPrice = this.dataset.courtPrice;
            const input = document.getElementById('selected_court_id');
            if (input) input.value = courtId;
            updateSummary('court', courtName);
            updateSummaryTotal();
        });
    });

    // Booking — time slot selection
    document.querySelectorAll('.time-slot:not(.booked)').forEach(slot => {
        slot.addEventListener('click', function () {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            const time = this.dataset.time;
            const input = document.getElementById('selected_time');
            if (input) input.value = time;
            updateSummary('time', time);
        });
    });

    // Payment method selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const method = this.dataset.method;
            const input = document.getElementById('payment_method');
            if (input) input.value = method;
        });
    });

    // Update booking summary
    function updateSummary(field, value) {
        const el = document.getElementById('summary-' + field);
        if (el) el.textContent = value;
    }

    function updateSummaryTotal() {
        // Price comes from the selected court
        const selected = document.querySelector('.court-select-card.selected');
        if (selected) {
            const price = selected.dataset.courtPrice;
            const el = document.getElementById('summary-price');
            if (el) el.textContent = 'RM ' + price;
            const total = document.getElementById('summary-total');
            if (total) total.textContent = 'RM ' + price;
        }
    }

    // Date picker min date
    const dateInput = document.getElementById('booking_date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        dateInput.addEventListener('change', function () {
            updateSummary('date', this.value);
            // Reload time slots for selected date + court via AJAX
            loadTimeSlots();
        });
    }

    function loadTimeSlots() {
        const date = document.getElementById('booking_date')?.value;
        const courtId = document.getElementById('selected_court_id')?.value;
        if (!date || !courtId) return;

        fetch(`/booking/slots?date=${date}&court_id=${courtId}`)
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('time-slots-container');
                if (!container) return;
                container.innerHTML = '';
                data.forEach(slot => {
                    const div = document.createElement('div');
                    div.className = 'col-6 col-md-3 col-lg-2';
                    div.innerHTML = `<div class="time-slot ${slot.booked ? 'booked' : ''}" 
                        data-time="${slot.time}" 
                        ${slot.booked ? 'title="Already booked"' : ''}>
                        ${slot.time}
                    </div>`;
                    container.appendChild(div);
                });
                // Re-bind click events
                document.querySelectorAll('.time-slot:not(.booked)').forEach(slot => {
                    slot.addEventListener('click', function () {
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        const input = document.getElementById('selected_time');
                        if (input) input.value = this.dataset.time;
                        updateSummary('time', this.dataset.time);
                    });
                });
            });
    }

    // Admin — delete confirmation
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    // QR print
    const printBtn = document.getElementById('print-qr');
    if (printBtn) {
        printBtn.addEventListener('click', () => window.print());
    }

    // Profile form validation
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            const phone = document.getElementById('phone').value;
            const phoneRegex = /^[0-9+\-\s]{8,15}$/;
            if (phone && !phoneRegex.test(phone)) {
                e.preventDefault();
                showFieldError('phone', 'Please enter a valid phone number.');
            }
        });
    }

    function showFieldError(fieldId, msg) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        field.classList.add('is-invalid');
        let feedback = field.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.insertBefore(feedback, field.nextSibling);
        }
        feedback.textContent = msg;
    }

    // Animate stat numbers
    document.querySelectorAll('.stat-number, .stat-card-value').forEach(el => {
        const target = parseInt(el.textContent.replace(/\D/g, ''));
        if (isNaN(target) || target === 0) return;
        let start = 0;
        const duration = 1200;
        const step = Math.ceil(target / (duration / 16));
        const prefix = el.textContent.match(/^[^0-9]*/)?.[0] || '';
        const suffix = el.textContent.match(/[^0-9]*$/)?.[0] || '';
        const timer = setInterval(() => {
            start = Math.min(start + step, target);
            el.textContent = prefix + start.toLocaleString() + suffix;
            if (start >= target) clearInterval(timer);
        }, 16);
    });

    // Navbar active link
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
});
