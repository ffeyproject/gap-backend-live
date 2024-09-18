$('#trnkartuprosesdyeing-kartu_proses_id').val(null);
$('#trnkartuprosesdyeing-kartu_proses_id').empty();
$('#trnkartuprosesdyeing-kartu_proses_id').trigger('change');

$('#trnkartuprosesdyeing-wo_color_id').val(null);
$('#trnkartuprosesdyeing-wo_color_id').empty();
$('#trnkartuprosesdyeing-wo_color_id').trigger('change');

let params = e.params;
let data = params.data; //console.log(data);

$.ajax({
    method: 'POST',
    /*beforeSend: function (jqXHR, settings) {
        $.blockUI({
            message: '<h1>Processing</h1>',
            css: { border: '3px solid #a00' }
        });
    },*/
    data:{data:data.id},
    url: lookupWoColorUrl,
    error: function(jqXHR, textStatus, errorThrown ){
        var errorObj;
        try {
            errorObj = jQuery.parseJSON(jqXHR.responseText);
            if(typeof errorObj !='object'){
                errorObj = {name:"Error", message:jqXHR.responseText};
            }
        } catch (e) {
            errorObj = {name:"Error", message:jqXHR.responseText};
        }

        //$.unblockUI();

        $.alert({
            title: errorObj.name,
            content: errorObj.message
        });
    },
    success: function(data){
        //$.unblockUI();
        //console.log(data);

        $.each(data, function( key, value ) {
            let dataNewSelection = {id: value.id, text: value.moColor.color};

            if (! $('#trnkartuprosesdyeing-wo_color_id').find("option[value='" + dataNewSelection.id + "']").length) {
                let newOption = new Option(dataNewSelection.text, dataNewSelection.id, false, false);
                //$('#trnkartuprosesdyeing-wo_color_id').append(newOption).trigger('change');
                $('#trnkartuprosesdyeing-wo_color_id').append(newOption);
            }
        });

        $('#trnkartuprosesdyeing-wo_color_id').trigger('change');
    }
});

/*let dataNewSelectionWoId = {id: data.wo_id, text: data.wo_no};

if ($('#trnkartuprosesdyeing-wo_id').find("option[value='" + dataNewSelectionWoId.id + "']").length) {
    $('#trnkartuprosesdyeing-wo_id').val(dataNewSelectionWoId.id).trigger('change');
}else{
    let newOption = new Option(dataNewSelectionWoId.text, dataNewSelectionWoId.id, true, true);
    $('#trnkartuprosesdyeing-wo_id').append(newOption).trigger('change');
}

$('#trnkartuprosesdyeing-wo_id').val(data.wo_id);
$('#trnkartuprosesdyeing-wo_id').trigger('change');*/