// VMS Main JS — Form wizards, AJAX, validation
$(document).ready(function() {

    // === STEP WIZARD ===
    let currentStep = 1;
    const totalSteps = $('.form-step').length;

    function showStep(step) {
        $('.form-step').removeClass('active');
        $('#step-' + step).addClass('active');
        $('.step').removeClass('active completed');
        for (let i = 1; i <= totalSteps; i++) {
            if (i < step) $('.step[data-step="' + i + '"]').addClass('completed');
            if (i === step) $('.step[data-step="' + i + '"]').addClass('active');
        }
        for (let i = 1; i < step; i++) {
            $('.step-divider').eq(i - 1).addClass('completed');
        }
        currentStep = step;
    }

    $(document).on('click', '.btn-next', function() {
        if (validateStep(currentStep) && currentStep < totalSteps) {
            showStep(currentStep + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    $(document).on('click', '.btn-prev', function() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    $(document).on('click', '.step', function() {
        const targetStep = parseInt($(this).data('step'));
        if (targetStep === currentStep) return;

        // If moving forward, validate current step
        if (targetStep > currentStep) {
            if (!validateStep(currentStep)) return;
        }

        showStep(targetStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function validateStep(step) {
        let valid = true;
        $('#step-' + step).find('input, select, textarea').each(function() {
            if (typeof this.checkValidity === 'function' && !this.checkValidity()) {
                $(this).addClass('is-invalid').addClass('is-invalid-shake');
                const $el = $(this);
                setTimeout(function() { $el.removeClass('is-invalid-shake'); }, 500);
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Remove error outline in real-time when user types
        $('#step-' + step).off('input change', 'input, select, textarea').on('input change', 'input, select, textarea', function() {
            if (typeof this.checkValidity === 'function' && this.checkValidity()) {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) showToast('Please fill all required fields correctly.', 'warning');
        return valid;
    }

    // === COMPANY TYPE → SUBTYPE AJAX ===
    $('#company_type').on('change', function() {
        const typeId = $(this).val();
        const $sub = $('#company_subtype');
        $sub.html('<option value="">Loading...</option>');
        if (!typeId) { $sub.html('<option value="">-- Select Type First --</option>'); return; }
        $.get(APP_URL + '/api/get_subtypes.php', { type_id: typeId }, function(res) {
            $sub.html('<option value="">-- Select Sub-Type --</option>');
            if (res.subtypes) {
                res.subtypes.forEach(function(s) {
                    $sub.append('<option value="' + s.id + '">' + s.subtype_name + '</option>');
                });
            }
        }, 'json').fail(function() { $sub.html('<option value="">-- Error loading --</option>'); });
    });

    // === TOAST NOTIFICATIONS ===
    window.showToast = function(msg, type) {
        type = type || 'info';
        const icons = { success: '✓', danger: '✕', warning: '⚠', info: 'ℹ' };
        
        // Remove existing toasts to prevent stacking issues (optional, but keeps it clean)
        $('.alert-toast').remove();
        
        const $t = $('<div class="alert alert-vms alert-' + type + ' fade-in alert-toast" style="position:fixed;bottom:30px;right:30px;z-index:9999;min-width:300px;box-shadow:var(--shadow-lg); border-radius:var(--radius);">' +
            '<span style="font-size:1.2rem; margin-right:10px;">' + (icons[type]||'') + '</span> ' + msg + '</div>');
        $('body').append($t);
        setTimeout(function() { $t.fadeOut(400, function() { $t.remove(); }); }, 4000);
    };

    // === GLOBAL ACTION MODAL ===
    window.showActionModal = function(title, message, btnText, btnColor, requireRemarks, callback) {
        const $modal = $('#globalActionModal');
        $modal.find('#actionModalTitle').text(title);
        $modal.find('#actionModalMessage').text(message);
        
        const $remarks = $modal.find('#actionModalRemarks');
        $remarks.val(''); // Clear previous
        
        if (requireRemarks) {
            $remarks.attr('placeholder', 'Remarks are required...').attr('required', true);
        } else {
            $remarks.attr('placeholder', 'Optional remarks...').removeAttr('required');
        }

        const $btn = $modal.find('#actionModalConfirmBtn');
        $btn.text(btnText)
            .removeClass()
            .addClass('btn rounded-pill px-4 text-white')
            .css('background-color', btnColor)
            .css('border-color', btnColor);

        // Remove previous event listeners
        $btn.off('click').on('click', function() {
            const remarks = $remarks.val().trim();
            if (requireRemarks && remarks === '') {
                showToast('Please provide remarks to proceed.', 'warning');
                $remarks.focus();
                return;
            }
            callback(remarks);
            $modal.modal('hide');
        });

        // Show modal using Bootstrap jQuery API
        $modal.modal('show');
    };

    // === FORM FIELD TOGGLE (Admin) ===
    $(document).on('change', '.toggle-field', function() {
        const $el = $(this);
        $.post(APP_URL + '/api/toggle_field.php', {
            form_type:  $el.data('form'),
            field_name: $el.data('field'),
            key:        $el.data('key'),
            value:      $el.is(':checked') ? 1 : 0
        }, function(res) {
            if (res.success) showToast('Field updated', 'success');
            else showToast('Error saving', 'danger');
        }, 'json');
    });

    // === INPUT FOCUS EFFECTS ===
    $('.form-control-vms').on('focus', function() {
        $(this).closest('.mb-3').find('.form-label').css('color', 'var(--primary)');
    }).on('blur', function() {
        $(this).closest('.mb-3').find('.form-label').css('color', '');
    });

    // === CONFIRM DELETE ===
    window.confirmAction = function(msg, callback) {
        if (confirm(msg)) callback();
    };
});
