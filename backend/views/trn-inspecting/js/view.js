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

function saveLotNoItems(itemsPayload, $inputsToMark, $buttonsToMark) {
    if (typeof updateLotNoUrl === 'undefined' || !itemsPayload.length) {
        return;
    }

    var $statusEl = $('#lot-no-save-status');
    $statusEl.removeClass('text-danger text-success').addClass('text-muted').text('Menyimpan...');
    if ($buttonsToMark && $buttonsToMark.length) {
        $buttonsToMark.prop('disabled', true);
    }

    $.ajax({
        method: 'POST',
        url: updateLotNoUrl,
        data: { items: itemsPayload },
        success: function(res) {
            if (res && res.success && res.items) {
                $.each(res.items, function(itemId, savedVal) {
                    var $inp = $('.lot-no-input[data-id="' + itemId + '"]');
                    $inp.val(savedVal).attr('data-original', savedVal).data('original', savedVal);
                    $inp.css('border-color', '#00a65a');
                    var $btn = $('.btn-save-lot-no[data-id="' + itemId + '"]');
                    $btn.find('i').removeClass('fa-save').addClass('fa-check text-success');
                    setTimeout(function() {
                        $inp.css('border-color', '');
                        $btn.find('i').removeClass('fa-check text-success').addClass('fa-save');
                    }, 1500);
                });
                $statusEl.removeClass('text-muted text-danger').addClass('text-success').text('No Lot tersimpan (' + res.count + ' roll/pcs).');
            }
        },
        error: function(jqXHR) {
            var msg = 'Gagal menyimpan No Lot.';
            try {
                var err = jQuery.parseJSON(jqXHR.responseText);
                if (err && err.message) {
                    msg = err.message;
                }
            } catch (e) {}
            if ($inputsToMark && $inputsToMark.length) {
                $inputsToMark.css('border-color', '#dd4b39');
            }
            $statusEl.removeClass('text-muted text-success').addClass('text-danger').text(msg);
        },
        complete: function() {
            if ($buttonsToMark && $buttonsToMark.length) {
                $buttonsToMark.prop('disabled', false);
            }
        }
    });
}

$(document).on('input', '.lot-no-input', function() {
    var $inp = $(this);
    var original = String($inp.attr('data-original') || '');
    if ($inp.val().trim() !== original.trim()) {
        $inp.css('border-color', '#f39c12');
    } else {
        $inp.css('border-color', '');
    }
});

$(document).on('keydown', '.lot-no-input', function(e) {
    if (e.key === 'Enter' || e.which === 13) {
        e.preventDefault();
        var $inputs = $('.lot-no-input');
        var idx = $inputs.index(this);
        $(this).trigger('change');
        if (idx >= 0 && idx + 1 < $inputs.length) {
            $inputs.eq(idx + 1).focus().select();
        }
    }
});

$(document).on('change', '.lot-no-input', function() {
    var $inp = $(this);
    var itemId = $inp.data('id');
    var currentVal = $inp.val().trim();
    var originalVal = String($inp.attr('data-original') || '').trim();
    if (currentVal === originalVal) {
        return;
    }
    var $btn = $('.btn-save-lot-no[data-id="' + itemId + '"]');
    saveLotNoItems([{ id: itemId, lot_no: currentVal }], $inp, $btn);
});

$(document).on('click', '.btn-save-lot-no', function() {
    var itemId = $(this).data('id');
    var $inp = $('.lot-no-input[data-id="' + itemId + '"]');
    saveLotNoItems([{ id: itemId, lot_no: $inp.val().trim() }], $inp, $(this));
});

$(document).on('click', '#btn-save-all-lot-no', function() {
    var payload = [];
    var $changedInputs = $();
    $('.lot-no-input').each(function() {
        var $inp = $(this);
        var currentVal = $inp.val().trim();
        var originalVal = String($inp.attr('data-original') || '').trim();
        if (currentVal !== originalVal) {
            payload.push({ id: $inp.data('id'), lot_no: currentVal });
            $changedInputs = $changedInputs.add($inp);
        }
    });

    if (!payload.length) {
        $('#lot-no-save-status').removeClass('text-danger text-success').addClass('text-muted').text('Tidak ada perubahan No Lot.');
        return;
    }

    saveLotNoItems(payload, $changedInputs, $(this));
});