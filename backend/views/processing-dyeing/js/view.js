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

function gantiWarna(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Pilih Warna:</label>' +
            '<select class="form-control" id="SelectWarna"></select>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('#SelectWarna').val();
                    if(!ctn){
                        $.alert('Harap pilih warna!!');
                        return false;
                    }

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
                                content: "Warna berhasil diganti.",
                                buttons: {
                                    ok: function () {
                                        window.location.reload();
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

            // get reference to select element
            var sel = document.getElementById('SelectWarna');

            $.each(warnaList, function( index, value ) {
                console.log(value);

                // create new option element
                var opt = document.createElement('option');

                // create text node to add to option element (opt)
                opt.appendChild( document.createTextNode(value.moColor.color) );

                // set value property of opt
                opt.value = value.id;

                // add opt to end of select box (sel)
                sel.appendChild(opt);
            });
        }
    });
}

function gantiWo(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label for="InputNoWo">Masukan Nomor WO:</label>' +
            '<input type="text" class="form-control" id="InputNoWo">' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('#InputNoWo').val();
                    if(!ctn){
                        $.alert('Harap isi nomor WO!!');
                        return false;
                    }

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
                                content: "WO Berhasil diganti",
                                buttons: {
                                    ok: function () {
                                        window.location.reload();
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

function gantiKePfp(event, label){
    event.preventDefault();
    let button = $(event.currentTarget);
    let href = button.attr('href');

    $.confirm({
        title: label,
        //columnClass: 'col-md-6',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label for="InputOpfp">Masukan Nomor Order PFP:</label>' +
            '<input type="text" class="form-control" id="InputOpfp">' +
            '<div class="text-danger input-opfp-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    let ctn = this.$content.find('#InputOpfp').val();
                    if(!ctn){
                        this.$content.find('.input-opfp-hint').html('Harap masukan nomor order PFP !!');
                        return false;
                    }else {
                        this.$content.find('.input-opfp-hint').html('');
                    }

                    $.ajax({
                        method: 'POST',
                        beforeSend: function (jqXHR, settings) {
                            $.blockUI({
                                message: '<h1>Processing</h1>',
                                css: { border: '3px solid #a00' }
                            });
                        },
                        data:{no_order_pfp:ctn},
                        url: href,
                        error: function(jqXHR, textStatus, errorThrown ){
                            let errorObj;
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
                                content: "Ganti ke PFP berhasil",
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
            let jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.formSubmit.trigger('click'); // reference the button and click it
            });
        }
    });
}