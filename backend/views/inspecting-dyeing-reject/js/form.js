(function () {
    var $form = $('#InspectingDyeingRejectForm');
    var $checks = $form.find('.idr-check');
    var $checkAll = $form.find('#idrCheckAll');

    function esc(text) {
        return $('<div>').text(text).html();
    }

    function fmt(number) {
        return number.toLocaleString('id-ID', {maximumFractionDigits: 2});
    }

    function round(number) {
        return Math.round(number * 100) / 100;
    }

    function visibleChecks() {
        return $checks.filter(function () {
            return !$(this).closest('tr').hasClass('hidden');
        });
    }

    function bump(selector) {
        var $el = $form.find(selector).addClass('bump');
        setTimeout(function () { $el.removeClass('bump'); }, 150);
    }

    function sync() {
        var total = $checks.length;
        var rejectCount = 0, rejectQty = 0, keepQty = 0;

        $checks.each(function () {
            var qty = parseFloat($(this).data('qty')) || 0;
            var $row = $(this).closest('tr');
            var $chip = $row.find('.idr-chip');
            if (this.checked) {
                rejectCount++;
                rejectQty += qty;
                $row.addClass('is-selected');
                $chip.attr('class', 'idr-chip idr-chip-reject').text('Processing');
            } else {
                keepQty += qty;
                $row.removeClass('is-selected');
                $chip.attr('class', 'idr-chip idr-chip-keep').text('Inspecting');
            }
        });

        var keepCount = total - rejectCount;
        rejectQty = round(rejectQty);
        keepQty = round(keepQty);

        $form.find('#idrRejectCount').text(rejectCount);
        $form.find('#idrRejectQty').text(fmt(rejectQty));
        $form.find('#idrKeepCount').text(keepCount);
        $form.find('#idrKeepQty').text(fmt(keepQty));
        $form.find('#inspectingdyeingreject-pcs').val(rejectCount);
        $form.find('#inspectingdyeingreject-total_qty').val(rejectQty);

        var $visible = visibleChecks();
        var visibleChecked = $visible.filter(':checked').length;
        $checkAll.prop('checked', $visible.length > 0 && visibleChecked === $visible.length);
        $checkAll.prop('indeterminate', visibleChecked > 0 && visibleChecked < $visible.length);

        var nomorKartu = esc(idrConfig.nomorKartu);
        var $notice = $form.find('#idrNotice');
        var $submit = $form.find('#idrSubmit');
        var $label = $form.find('#idrSubmitLabel');

        if (rejectCount === 0) {
            $notice.attr('class', 'idr-notice idr-notice-none')
                .html('<strong>Belum ada roll dipilih.</strong> Centang minimal satu roll yang akan dikembalikan ke processing.');
            $submit.prop('disabled', true);
            $label.text('Kembalikan');
        } else if (rejectCount === total) {
            $notice.attr('class', 'idr-notice idr-notice-full')
                .html('<span class="glyphicon glyphicon-info-sign"></span> <strong>Seluruh kartu ' + nomorKartu + '</strong> (' + total + ' roll) akan dikembalikan ke processing.');
            $submit.prop('disabled', false);
            $label.text('Kembalikan Semua (' + total + ' Roll)');
        } else {
            $notice.attr('class', 'idr-notice idr-notice-partial')
                .html('<span class="glyphicon glyphicon-info-sign"></span> <strong>' + rejectCount + ' roll</strong> dipindah ke kartu baru <strong>' + esc(idrConfig.nomorKartuBaru) + '</strong> dan kembali ke processing. '
                    + '<strong>' + keepCount + ' roll</strong> tetap di kartu <strong>' + nomorKartu + '</strong> untuk diinspect.');
            $submit.prop('disabled', false);
            $label.text('Kembalikan ' + rejectCount + ' Roll');
        }
    }

    function onSelectionChange() {
        sync();
        bump('#idrTileReject');
        bump('#idrTileKeep');
    }

    $checks.on('change', onSelectionChange);

    $form.on('click', '.idr-row', function (e) {
        if ($(e.target).is('input')) {
            return;
        }
        var check = $(this).find('.idr-check')[0];
        check.checked = !check.checked;
        onSelectionChange();
    });

    $checkAll.on('change', function () {
        visibleChecks().prop('checked', this.checked);
        onSelectionChange();
    });

    $form.on('click', '[data-idr-action]', function () {
        var action = $(this).data('idr-action');
        visibleChecks().each(function () {
            this.checked = action === 'all' ? true : (action === 'none' ? false : !this.checked);
        });
        onSelectionChange();
    });

    $form.find('#idrSearch').on('input', function () {
        var term = $.trim($(this).val()).toLowerCase();
        var visible = 0;
        $form.find('.idr-row').each(function () {
            var match = term === '' || String($(this).data('search')).indexOf(term) !== -1;
            $(this).toggleClass('hidden', !match);
            if (match) {
                visible++;
            }
        });
        $form.find('#idrEmpty').toggle(visible === 0);
        sync();
    });

    sync();
})();

$('#InspectingDyeingRejectForm').on('afterInit', function () {
    $('#InspectingDyeingRejectForm').on('beforeSubmit', function () {
        var $yiiform = $(this);
        $.ajax({
            type: $yiiform.attr('method'),
            url: $yiiform.attr('action'),
            data: $yiiform.serializeArray(),
            beforeSend: function(jqXHR, settings){
                $('.modal-content').block({
                    message: '<h1>Processing</h1>',
                    css: { border: '3px solid #a00' }
                });
            },
            success: function(data, textStatus, jqXHR){
                $('.modal-content').unblock();
                if(data.success) {
                    $("#trnInspectingModal").modal("hide");
                    location.replace(data.data);
                } else if (data.validation) {
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                } else {
                    // incorrect server response
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                $('.modal-content').unblock();
                console.log(jqXHR);
                $.confirm({
                    title: textStatus,
                    content: jqXHR.responseText,
                    buttons: {
                        close: function () {},
                    }
                });
            },
        });

        return false; // prevent default form submission
    });
});