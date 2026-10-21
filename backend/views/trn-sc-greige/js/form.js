(function() {
    var currentGreigeGroup = null;

    function checkProcessAndToggle() {
        var processVal = parseInt($('#trnscgreige-process').val());
        if (processVal === processPrintingVal) {
            $('#printing-finish-calculator').slideDown(200);
            fetchGreigeGroupAndCalculate();
        } else {
            $('#printing-finish-calculator').slideUp(200);
        }
    }

    function fetchGreigeGroupAndCalculate() {
        var greigeGroupId = $('#trnscgreige-greige_group_id').val();
        if (greigeGroupId) {
            $.ajax({
                url: ajaxGetGreigeGroupUrl,
                type: 'GET',
                dataType: 'json',
                data: { id: greigeGroupId },
                success: function(response) {
                    var data = response.data ? response.data : response;

                    if (data) {
                        currentGreigeGroup = data;
                        updateGreigeGroupInfo();
                        recalculateBatchQty();

                        // Tampilkan info lebar kain berdasarkan data yang diteruskan langsung dari model PHP
                        if (data.lebar_kain) {
                            // Mengambil label teks langsung dari mapping yang bersumber dari model PHP
                            var namaLebarKain = lebarKainMap[data.lebar_kain] || data.lebar_kain;
                            console.log(lebarKainMap);
                            

                            $('#greige-lebar-info').html(
                                '<i class="fa fa-info-circle text-info"></i> Standar lebar kain: <strong>' + namaLebarKain +  '</strong>'
                            );
                        } else {
                            $('#greige-lebar-info').html('');
                        }
                    }
                },
                error: function() {
                    console.log('Gagal mengambil data greige group.');
                }
            });
        } else {
            currentGreigeGroup = null;
            $('#greige-group-info').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Pilih Greige Group terlebih dahulu untuk kalkulasi otomatis.</span>');
            $('#greige-lebar-info').html('');
        }
    }

    function updateGreigeGroupInfo() {
        if (currentGreigeGroup) {
            var infoText = '<i class="fa fa-info-circle text-primary"></i> <strong>' + currentGreigeGroup.nama_kain + '</strong>' +
                ' | 1 Batch = ' + (Math.round(currentGreigeGroup.qty_finish_to_meter * 100) / 100).toLocaleString() + ' M' +
                ' (' + (Math.round(currentGreigeGroup.qty_finish_to_yard * 100) / 100).toLocaleString() + ' Yard)' +
                ' | Susut: ' + currentGreigeGroup.nilai_penyusutan + '%';
            $('#greige-group-info').html(infoText);
        }
    }

    function recalculateBatchQty() {
        var processVal = parseInt($('#trnscgreige-process').val());
        if (processVal !== processPrintingVal) {
            return;
        }

        var finishType = $('input[name="qty_finish_type"]:checked').val();
        var finishVal = parseFloat($('#qty_finish_val').val());

        if (finishType === 'meter') {
            $('#qty_finish_label').text('Jumlah Qty Finish (Meter):');
        } else {
            $('#qty_finish_label').text('Jumlah Qty Finish (Yard):');
        }

        if (currentGreigeGroup && !isNaN(finishVal) && finishVal > 0) {
            var oneBatchFinish = (finishType === 'meter') ? currentGreigeGroup.qty_finish_to_meter : currentGreigeGroup.qty_finish_to_yard;
            if (oneBatchFinish > 0) {
                var batchQty = finishVal / oneBatchFinish;
                var formattedBatch = Math.round(batchQty * 10000) / 10000;
                $('#trnscgreige-qty').val(formattedBatch);
                
                var unitLabel = (finishType === 'meter') ? 'Meter' : 'Yard';
                $('#greige-group-info').html(
                    '<span class="text-success" style="font-size: 13px;"><i class="fa fa-check-circle"></i> <strong>' + 
                    finishVal.toLocaleString() + ' ' + unitLabel + ' = ' + formattedBatch + ' Batch</strong>' +
                    ' (1 Batch = ' + (Math.round(oneBatchFinish * 100) / 100).toLocaleString() + ' ' + unitLabel + ')</span>'
                );
            }
        }
    }

    // Event listeners
    $('#trnscgreige-process').on('change', function() {
        checkProcessAndToggle();
    });

    $('#trnscgreige-greige_group_id').on('change', function() {
        fetchGreigeGroupAndCalculate();
    });

    $('input[name="qty_finish_type"]').on('change', function() {
        recalculateBatchQty();
    });

    $('#qty_finish_val').on('input keyup change', function() {
        recalculateBatchQty();
    });

    // Initial check on load
    checkProcessAndToggle();
    if ($('#trnscgreige-greige_group_id').val()) {
        fetchGreigeGroupAndCalculate();
    }
})();

// Form Submission AJAX (Yii2 ActiveForm)
$('#scGreigeForm').on('beforeSubmit', function () {
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
                $("#trnScModal").modal("hide");
                $.pjax.reload({container:'#ScGreigeItems-pjax'});
            } else if (data.validation) {
                $yiiform.yiiActiveForm('updateMessages', data.validation, true);
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
    return false;
});