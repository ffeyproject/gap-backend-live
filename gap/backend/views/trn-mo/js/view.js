function closeMo(event){
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
            '<span id="CloseMoErrorTag" class="text-danger"></span>' +
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
                        $("#CloseMoErrorTag").html("Harap masukan keterangan closing!!");
                        return false;
                    }

                    $("#CloseMoErrorTag").html("");

                    postingCloseMo(href, ctn);
                    //$.alert('Your name is ' + ctn);
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

function postingCloseMo(href, ctn) {
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
                content: "MO telah close.",
                buttons: {
                    ok: function () {
                        location.reload();
                    }
                }
            });
        }
    });
}

function batalMo(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Tulis keterangan Pembatalan:</label>' +
            '<textarea class="note form-control" rows="6"></textarea>' +
            '<span id="BatalMoErrorTag" class="text-danger"></span>' +
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
                        $("#BatalMoErrorTag").html("Harap masukan keterangan pembatalan!!");
                        return false;
                    }

                    $("#BatalMoErrorTag").html("");

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
                                content: "MO telah dibatalkan.",
                                buttons: {
                                    ok: function () {
                                        location.reload();
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
