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