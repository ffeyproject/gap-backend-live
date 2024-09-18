function terimaKartuProses(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Masukan Berat:</label>' +
            '<input type="number" class="berat form-control" value="'+beratPeneerimaan+'"></input>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.berat').val();
                    if(!ctn){
                        $.alert('Harap masukan nilai berat!!');
                        return false;
                    }

                    posting(href, ctn, "Kartu Proses berhasil diterima.");
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

function tolakKartuProses(event){
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

                    posting(href, ctn, "Kartu Proses berhasil ditolak.");
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

                    posting(href, ctn, "Warna berhasil diganti.", true);
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

                    posting(href, ctn, "WO berhasil diganti.", true);
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

function posting(href, ctn, message, reload=false) {
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
                content: message,
                buttons: {
                    ok: function () {
                        if(reload){
                            window.location.reload();
                        }else {
                            window.location.replace(indexUrl);
                        }
                    }
                }
            });
        }
    });
}