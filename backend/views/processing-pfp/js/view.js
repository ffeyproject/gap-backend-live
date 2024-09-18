function memoPg(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        columnClass: 'col-md-12',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<textarea class="form-control kartu-proses-textarea" rows="6"></textarea>' +
            '<div class="text-danger kartu-proses-textarea-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.kartu-proses-textarea').val();
                    if(!ctn){
                        this.$content.find('.kartu-proses-textarea-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingPg(href, ctn, label);
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

function gantiDyeing(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        //columnClass: 'col-md-6',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label for="InputNoWo">Masukan Nomor WO:</label>' +
            '<input type="text" class="form-control" id="InputNoWo">' +
            '<div class="text-danger input-no-wo-hint"></div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label for="InputNoWo">Masukan Color:</label>' +
            '<input type="text" class="form-control" id="InputColor">' +
            '<div class="text-danger input-color-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    let ctnWo = this.$content.find('#InputNoWo').val();
                    if(!ctnWo){
                        this.$content.find('.input-no-wo-hint').html('Harap masukan No. WO !!');
                        return false;
                    }else {
                        this.$content.find('.input-no-wo-hint').html('');
                    }

                    let ctnColor = this.$content.find('#InputColor').val();
                    if(!ctnColor){
                        this.$content.find('.input-color-hint').html('Harap masukan color !!');
                        return false;
                    }else {
                        this.$content.find('.input-color-hint').html('');
                    }

                    $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $.blockUI({
                                message: '<h1>Processing</h1>',
                                css: { border: '3px solid #a00' }
                            });
                        },
                        data:{no_wo:ctnWo, color:ctnColor},
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
                        success: function(response){
                            $.unblockUI();
                            console.log(response);
                            $.alert({
                                title: "Berhasil",
                                content: "Ganti kartu dyeing berhasil",
                                buttons: {
                                    ok: function () {
                                        location.reload();
                                        //window.location.replace(indexUrl);
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

//POSTING--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function postingPg(href, ctn, label) {
    //console.log(href);console.log(ctn);
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
            //console.log(data);
            $.alert({
                title: "Berhasil",
                content: "Proses " + label + " telah diinput.",
                buttons: {
                    ok: function () {
                        //location.reload();
                        window.location.replace(indexUrl);
                    }
                }
            });
        }
    });
}
//POSTING--------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function setData(event, flag){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    console.log(flag);console.log(href);

    $.confirm({
        title: 'Konfirmasi!',
        content: 'Anda yakin akan menyetujui MO ini?',
        buttons: {
            ok: function () {
                $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $.blockUI({
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
                                content: "MO berhasil disetujui.",
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
}

function gantiMotif(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        //columnClass: 'col-md-6',
        content: '' +
            '<form action="" class="formName">' +
                '<div class="form-group">' +
                    '<label for="InputMotif">Masukan Nama Motif:</label>' +
                    '<input type="text" class="form-control" id="InputMotif">' +
                    '<div class="text-danger input-motif-hint"></div>' +
                '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    let ctn = this.$content.find('#InputMotif').val();
                    if(!ctn){
                        this.$content.find('.input-motif-hint').html('Harap masukan nama motif !!');
                        return false;
                    }else {
                        this.$content.find('.input-motif-hint').html('');
                    }

                    $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $.blockUI({
                                message: '<h1>Processing</h1>',
                                css: { border: '3px solid #a00' }
                            });
                        },
                        data:{nama_motif:ctn},
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
                        success: function(response){
                            $.unblockUI();
                            console.log(response);
                            $.alert({
                                title: "Berhasil",
                                content: "Ganti motif berhasil",
                                buttons: {
                                    ok: function () {
                                        location.reload();
                                        //window.location.replace(indexUrl);
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