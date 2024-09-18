$('#trnbelikainjadi-wo_color_id').val(null);
$('#trnbelikainjadi-wo_color_id').empty();
$('#trnbelikainjadi-wo_color_id').trigger('change');

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

            if (! $('#trnbelikainjadi-wo_color_id').find("option[value='" + dataNewSelection.id + "']").length) {
                let newOption = new Option(dataNewSelection.text, dataNewSelection.id, false, false);
                //$('#trnbelikainjadi-wo_color_id').append(newOption).trigger('change');
                $('#trnbelikainjadi-wo_color_id').append(newOption);
            }
        });

        $('#trnbelikainjadi-wo_color_id').trigger('change');
    }
});
