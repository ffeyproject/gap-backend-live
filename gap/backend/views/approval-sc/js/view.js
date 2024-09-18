function approveSc(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: 'Anda yakin akan menyetujui SC ini?',
        buttons: {
            ok: function () {
                $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $('.sc-view').block({
                                message: '<h1>Processing</h1>',
                                css: { border: '3px solid #a00' }
                            });
                        },
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

                            $('.sc-view').unblock();

                            $.alert({
                                title: errorObj.name,
                                content: errorObj.message
                            });
                        },
                        success: function(data){
                            $('.sc-view').unblock();
                            $.alert({
                                title: "Berhasil",
                                content: "SC berhasil disetujui.",
                                buttons: {
                                    ok: function () {
                                        window.location.replace(indexUrl);
                                    }
                                }
                            });
                        }
                    }
                );
            },
            batal: function () {}
        }
    });
    console.log(href);
}

function rejectSc(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Tulis keterangan Penolakan:</label>' +
            '<textarea class="note form-control" rows="6"></textarea>' +
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
                        $.alert('Harap masukan keterangan penolakan!!');
                        return false;
                    }

                    postingRejectSc(href, ctn);
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

function postingRejectSc(href, ctn) {
    $.ajax({
        method: 'POST',
        beforeSend: function (jqXHR, settings) {
            $('.sc-view').block({
                message: '<h1>Processing</h1>',
                css: { border: '3px solid #a00' }
            });
        },
        data:{reject_note:ctn},
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

            $('.sc-view').unblock();

            $.alert({
                title: errorObj.name,
                content: errorObj.message
            });
        },
        success: function(data){
            $('.sc-view').unblock();
            $.alert({
                title: "Berhasil",
                content: "SC telah ditolak.",
                buttons: {
                    ok: function () {
                        window.location.replace(indexUrl);
                    }
                }
            });
        }
    });
}