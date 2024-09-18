$('#trnkartuprosesmaklon-kartu_proses_id').val(null);
$('#trnkartuprosesmaklon-kartu_proses_id').empty();
$('#trnkartuprosesmaklon-kartu_proses_id').trigger('change');

$('#trnkartuprosesmaklon-wo_color_id').val(null);
$('#trnkartuprosesmaklon-wo_color_id').empty();
$('#trnkartuprosesmaklon-wo_color_id').trigger('change');

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

            if (! $('#trnkartuprosesmaklon-wo_color_id').find("option[value='" + dataNewSelection.id + "']").length) {
                let newOption = new Option(dataNewSelection.text, dataNewSelection.id, false, false);
                //$('#trnkartuprosesmaklon-wo_color_id').append(newOption).trigger('change');
                $('#trnkartuprosesmaklon-wo_color_id').append(newOption);
            }
        });

        $('#trnkartuprosesmaklon-wo_color_id').trigger('change');
    }
});

/*let dataNewSelectionWoId = {id: data.wo_id, text: data.wo_no};

if ($('#trnkartuprosesmaklon-wo_id').find("option[value='" + dataNewSelectionWoId.id + "']").length) {
    $('#trnkartuprosesmaklon-wo_id').val(dataNewSelectionWoId.id).trigger('change');
}else{
    let newOption = new Option(dataNewSelectionWoId.text, dataNewSelectionWoId.id, true, true);
    $('#trnkartuprosesmaklon-wo_id').append(newOption).trigger('change');
}

$('#trnkartuprosesmaklon-wo_id').val(data.wo_id);
$('#trnkartuprosesmaklon-wo_id').trigger('change');*/
