$('#dynamic-form').on('beforeSubmit', function () {
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
            console.log(data);

            if(data.success) {
                $("#kartuProsesPfpModal").modal("hide");
                $.confirm({
                    title: 'Sukses',
                    content: 'Processing telah diselesaikan dan masuk stok PFP.',
                    buttons: {
                        close: function () {location.reload();},
                    }
                });
            } else if (data.validation) {
                $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
            } else {
                // incorrect server response
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            $.confirm({
                title: textStatus,
                content: jqXHR.responseText,
                buttons: {
                    close: function () {},
                }
            });
        },
        complete: function(jqXHR, textStatus){
            $('.modal-content').unblock();
        }
    });

    return false; // prevent default form submission
});