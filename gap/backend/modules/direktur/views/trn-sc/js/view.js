function closeGreigeGroup(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Tulis keterangan Closing:</label>' +
            '<textarea class="note form-control" rows="6"></textarea>' +
            '<span id="CloseScGreigeErrorTag" class="text-danger"></span>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.note').val();
                    if(!ctn){
                        //$.alert('Harap masukan keterangan closing!!');
                        $("#CloseScGreigeErrorTag").html("Harap masukan keterangan closing!!");
                        return false;
                    }

                    $("#CloseScGreigeErrorTag").html("");

                    $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $.blockUI({
                                message: '<h1>Processing</h1>',
                                css: { border: '3px solid #a00' }
                            });
                        },
                        data:{data:ctn},
                        url: href,
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

                            $.unblockUI();

                            $.alert({
                                title: errorObj.name,
                                content: errorObj.message
                            });
                        },
                        success: function(data){
                            $.unblockUI();
                            $.alert({
                                title: "Berhasil",
                                content: "SC Greige telah close.",
                                buttons: {
                                    ok: function () {
                                        //location.reload();
                                        $.pjax.reload({container:'#ScGreigeItems-pjax'});
                                    }
                                }
                            });
                        }
                    });
                }
            },
            batal: function () {}
        },
        onContentReady: function(){
            // bind to events
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.formSubmit.trigger('click'); // reference the button and click it
            });
        }
    });
}
