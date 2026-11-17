$(function(){
    $( "#datepicker" ).datepicker({
        dateFormat: "yy-mm-dd",
    });

    var $stockSelect = $('#trnkartuprosesprintingitem-stock_ids');
    var $wrapper = $('#digital-cut-wrapper');

    function recalcCutTotal() {
        var total = 0;
        $('.input-qty-dipakai').each(function() {
            var v = parseFloat($(this).val()) || 0;
            total += v;
        });
        $('#sum-qty-dipakai').text(total.toFixed(2));
    }

    if ($wrapper.length > 0) {
        $stockSelect.on('select2:select', function (e) {
            var stock = e.params.data;
            if (!stock || !stock.id) return;

            var stockId = stock.id;
            if ($('#cut-row-' + stockId).length > 0) return;

            $('#row-empty-placeholder').remove();

            var stockData = stock.data || {};
            var panjangAsli = parseFloat(stockData.panjang_m);
            if (isNaN(panjangAsli) || panjangAsli <= 0) {
                var match = stock.text ? stock.text.match(/Panjang:\s*([0-9.,]+)M/i) : null;
                if (match) {
                    panjangAsli = parseFloat(match[1].replace(/,/g, ''));
                }
            }
            if (isNaN(panjangAsli)) panjangAsli = 0;

            var noDoc = stockData.no_document || '';
            var lapak = stockData.no_lapak ? (' | Lapak: ' + stockData.no_lapak) : '';
            var info = noDoc ? (noDoc + lapak) : $('<div>').html(stock.text).text().substring(0, 70);

            var rowHtml = '<tr id="cut-row-' + stockId + '" data-id="' + stockId + '">' +
                '<td><strong>' + info + '</strong><div class="text-muted small">ID Stock: ' + stockId + '</div></td>' +
                '<td class="text-right" style="vertical-align: middle;"><span>' + panjangAsli.toFixed(2) + '</span> M</td>' +
                '<td style="vertical-align: middle;">' +
                    '<div class="input-group input-group-sm">' +
                        '<input type="number" name="custom_qty[' + stockId + ']" id="input-qty-' + stockId + '" class="form-control input-qty-dipakai text-right" step="0.01" min="0.01" max="' + panjangAsli + '" value="' + panjangAsli + '" data-id="' + stockId + '" data-max="' + panjangAsli + '" required>' +
                        '<span class="input-group-addon">M</span>' +
                    '</div>' +
                '</td>' +
                '<td class="text-right" style="vertical-align: middle;"><span id="sisa-' + stockId + '" class="text-primary" style="font-weight: bold;">0.00</span> M</td>' +
                '<td class="text-center" style="vertical-align: middle;"><span id="badge-' + stockId + '" class="label label-success">Pakai Penuh</span></td>' +
            '</tr>';

            $('#tbl-cut-items-body').append(rowHtml);
            recalcCutTotal();
        });

        $stockSelect.on('select2:unselect', function (e) {
            var stockId = e.params.data ? e.params.data.id : null;
            if (stockId) {
                $('#cut-row-' + stockId).remove();
            }
            if ($('#tbl-cut-items-body tr').length === 0) {
                $('#tbl-cut-items-body').html('<tr id="row-empty-placeholder"><td colspan="5" class="text-center text-muted" style="padding: 12px;">Belum ada stock yang dipilih. Silakan cari dan pilih stock di atas.</td></tr>');
            }
            recalcCutTotal();
        });

        $stockSelect.on('select2:clear', function () {
            $('#tbl-cut-items-body').html('<tr id="row-empty-placeholder"><td colspan="5" class="text-center text-muted" style="padding: 12px;">Belum ada stock yang dipilih. Silakan cari dan pilih stock di atas.</td></tr>');
            recalcCutTotal();
        });

        $(document).on('input change keyup', '.input-qty-dipakai', function() {
            var $input = $(this);
            var stockId = $input.data('id');
            var max = parseFloat($input.data('max')) || 0;
            var val = parseFloat($input.val());

            if (isNaN(val)) val = 0;

            var sisa = Math.round((max - val) * 100) / 100;
            var $badge = $('#badge-' + stockId);
            var $sisa = $('#sisa-' + stockId);

            if (val > max) {
                $badge.removeClass('label-success label-warning').addClass('label-danger').text('Lebih dr stock!');
                $sisa.text('0.00');
            } else if (val <= 0) {
                $badge.removeClass('label-success label-warning').addClass('label-danger').text('Harus > 0');
                $sisa.text(max.toFixed(2));
            } else if (sisa > 0) {
                $sisa.text(sisa.toFixed(2));
                $badge.removeClass('label-success label-danger').addClass('label-warning').text('Potong (Sisa ' + sisa.toFixed(2) + 'M)');
            } else {
                $sisa.text('0.00');
                $badge.removeClass('label-warning label-danger').addClass('label-success').text('Pakai Penuh');
            }

            recalcCutTotal();
        });
    }
});

$('#KartuProsesItemForm').on('beforeSubmit', function () {
    var $yiiform = $(this);

    if ($('#digital-cut-wrapper').length > 0) {
        var hasError = false;
        var errorMsg = '';
        $('.input-qty-dipakai').each(function() {
            var max = parseFloat($(this).data('max')) || 0;
            var val = parseFloat($(this).val());
            if (isNaN(val) || val <= 0) {
                hasError = true;
                errorMsg = 'Qty dipakai harus lebih besar dari 0.';
                return false;
            }
            if (val > max) {
                hasError = true;
                errorMsg = 'Qty dipakai (' + val + 'M) melebihi qty stock asal (' + max + 'M).';
                return false;
            }
        });

        if (hasError) {
            $.alert({
                title: 'Peringatan',
                content: errorMsg,
                type: 'red'
            });
            return false;
        }
    }

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
                $("#kartuProsesPrintingModal").modal("hide");
                $.pjax.reload({container:'#KartuProsesPrintingItemsGrid-pjax'});
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