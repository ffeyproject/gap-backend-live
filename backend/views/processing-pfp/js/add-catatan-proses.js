$('#AddCatatanProsesForm').on('beforeSubmit', function () {
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
        success: function(response, textStatus, jqXHR){
            $('.modal-content').unblock();
            if(response.success) {
                $("#processingPfpModal").modal("hide");
                $("#CatatanProsesValue").html(response.data);
            } else if (response.validation) {
                $yiiform.yiiActiveForm('updateMessages', response.validation, true); // renders validation messages at appropriate places
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