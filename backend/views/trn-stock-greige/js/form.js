$("#trnstockgreige-no_document").on('change.yii', function(){
    let noDoc = $(this).val();
    if (!noDoc.trim()) {
        // is empty or whitespace
    }else{
        //console.log(noDoc);
        $.blockUI();

        $.get( urlCheckNoDoc + noDoc, function() {
            console.log( "success1" );
        })
            .done(function(data) {
                //console.log( 'done' );
                console.log(data);
                $('input[name="TrnStockGreige[no_lapak]"]').val(data.no_lapak);
                $("#trnstockgreige-grade").val(data.grade); $('#trnstockgreige-grade').trigger('change');
                $('input[name="TrnStockGreige[lot_lusi]"]').val(data.lot_lusi);
                $('input[name="TrnStockGreige[lot_pakan]"]').val(data.lot_pakan);
                //$('input[name="TrnStockGreige[no_set_lusi]"]').val(data.no_set_lusi);
                $('input[name="TrnStockGreige[pengirim]"]').val(data.pengirim);
                $('input[name="TrnStockGreige[mengetahui]"]').val(data.mengetahui);
                $("#trnstockgreige-status_tsd").val(data.status_tsd); $('#trnstockgreige-status_tsd').trigger('change');
                $("#trnstockgreige-asal_greige").val(data.asal_greige); $('#trnstockgreige-asal_greige').trigger('change');

                var greigeSelect = $('#trnstockgreige-greige_id');
                greigeSelect.val(null).trigger('change');
                var optionGreige = new Option(data.greige.nama_kain, data.greige.id, true, true);
                greigeSelect.append(optionGreige).trigger('change');
                // manually trigger the `select2:select` event
                greigeSelect.trigger({
                    type: 'select2:select',
                    params: {
                        data: data.greige
                    }
                });
            })
            .fail(function(data) {
                //console.log(data);
                let errCode = "Error";
                let errMsg = "Error";
                let errMsgJson = data.responseJSON;
                if(!errMsgJson){
                    errMsg = data.responseText;
                }else{
                    errMsg = errMsgJson.message;
                    errCode = errMsgJson.name;
                }

                $.alert({
                    title: errCode,
                    content: errMsg
                });
            })
            .always(function() {
                //console.log( "always" );
                $.unblockUI();
            });
    }
});