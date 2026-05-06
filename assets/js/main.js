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

    function validateStep(step) {
        let valid = true;
        $('#step-' + step + ' [required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if (!valid) showToast('Please fill all required fields.', 'warning');
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
        const $t = $('<div class="alert alert-vms alert-' + type + ' fade-in" style="position:fixed;top:80px;right:20px;z-index:9999;min-width:300px;box-shadow:0 8px 32px rgba(0,0,0,0.3)">' +
            '<span style="font-size:1.2rem">' + (icons[type]||'') + '</span> ' + msg + '</div>');
        $('body').append($t);
        setTimeout(function() { $t.fadeOut(400, function() { $t.remove(); }); }, 4000);
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
        $(this).closest('.mb-3').find('.form-label').css('color', 'var(--cyan)');
    }).on('blur', function() {
        $(this).closest('.mb-3').find('.form-label').css('color', '');
    });

    // === CONFIRM DELETE ===
    window.confirmAction = function(msg, callback) {
        if (confirm(msg)) callback();
    };
});
