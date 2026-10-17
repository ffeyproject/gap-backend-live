function ambil(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    var keys = $('#GdJadiGrid').yiiGridView('getSelectedRows');
    if(Array.isArray(keys) && keys.length){
        $.ajax({
            method: 'POST',
            beforeSend: function (jqXHR, settings) {
                $.blockUI({
                    message: '<h1>Processing</h1>',
                    css: { border: '3px solid #a00' }
                });
            },
            data:{formData:keys},
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
                $.confirm({
                    title: 'Berhasil!',
                    content: 'Seting stock berhasil',
                    buttons: {
                        ok: function () {
                            location.reload();
                        },
                    }
                });
            }
        });
    }else {
        $.alert({
            title: 'Peringatan!',
            content: 'Tidak ada data yang dipilih',
        });
    }
}

function kembalikan(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    var keys = $('#SiapKirimGrid').yiiGridView('getSelectedRows');
    if(Array.isArray(keys) && keys.length){
        $.ajax({
            method: 'POST',
            beforeSend: function (jqXHR, settings) {
                $.blockUI({
                    message: '<h1>Processing</h1>',
                    css: { border: '3px solid #a00' }
                });
            },
            data:{formData:keys},
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
                $.confirm({
                    title: 'Berhasil!',
                    content: 'Item pengiriman telah dikembalikan.',
                    buttons: {
                        ok: function () {
                            location.reload();
                        },
                    }
                });
            }
        });
    }else {
        $.alert({
            title: 'Peringatan!',
            content: 'Tidak ada data yang dipilih',
        });
    }
}

function changeAlias(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content:inputNamaKain,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.JGudang').val();
                    if(!ctn){
                        this.$content.find('.err-block').html("Harap masukan nama kain!!");
                        //$.alert('Harap masukan Piihan Jenis Gudang!!');
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
                        data:{formData:ctn},
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
                            console.log(data);
                            $.alert({
                                title: "Berhasil",
                                content: "Nama kain berhasil dirubah.",
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

function changeNote(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content:'<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Ubah Keterangan</label>' +
            '<input type="text" class="ChgKet form-control" maxLength="255">' +
            '<div class="err-block text-danger"></div></div></form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.ChgKet').val();
                    if(!ctn){
                        this.$content.find('.err-block').html("Harap masukan keterangan!!");
                        //$.alert('Harap masukan Piihan Jenis Gudang!!');
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
                        data:{formData:ctn},
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
                            console.log(data);
                            $.alert({
                                title: "Berhasil",
                                content: "Keterangan berhasil dirubah.",
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

function printDiv(div) {
    var fontSize = document.getElementById("SizeText") ? document.getElementById("SizeText").value : 12;
    var divContents = document.getElementById(div).innerHTML;

    var iframeId = 'print_iframe';
    var iframe = document.getElementById(iframeId);
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = iframeId;
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);
    }

    var doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><head><title>Print Surat Jalan</title>');
    doc.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    doc.write('<style type="text/css">');
    doc.write('@page { size: portrait; margin: 6mm 8mm; }');
    doc.write('@media print { html, body { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; } tr { page-break-inside: avoid; } }');
    doc.write('html, body { width: 100%; max-width: 780px; margin: 0 auto; padding: 5px; box-sizing: border-box; font-family: "Calibri", Arial, sans-serif; font-size:' + fontSize + 'px; letter-spacing: 0.5px; }');
    doc.write('table { width: 100%; font-size:' + fontSize + 'px; border-spacing: 0; border-collapse: collapse; letter-spacing: 0.5px; box-sizing: border-box; }');
    doc.write('th, td { padding: 4px 6px; box-sizing: border-box; }');
    doc.write('</style>');
    doc.write('</head><body>');
    doc.write(divContents);
    doc.write('</body></html>');
    doc.close();

    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }, 250);
}

function printDivPL(div) {
    var fontSize = document.getElementById("SizeText") ? document.getElementById("SizeText").value : 12;
    var pieceFontSize = document.getElementById("SizePieceText") ? document.getElementById("SizePieceText").value : 18;

    // Capture input values
    var balInputs = document.getElementsByClassName("balInput");
    var balValues = [];
    for (var i = 0; i < balInputs.length; i++) {
        balValues.push(balInputs[i].value);
    }

    var divContents = document.getElementById(div).innerHTML;

    // Create a temporary div element
    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = divContents;

    // Select all elements with class 'balInput' within the temporary div
    var tempBalInputs = tempDiv.querySelectorAll('.balInput');

    // Iterate through the balInputs and replace them with span elements
    tempBalInputs.forEach(function(tempBalInput, index) {
        var spanElement = document.createElement('span');
        spanElement.textContent = balValues[index];
        tempBalInput.parentNode.replaceChild(spanElement, tempBalInput);
    });

    var divContentFinals = tempDiv.innerHTML;

    var iframeId = 'print_iframe';
    var iframe = document.getElementById(iframeId);
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = iframeId;
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.appendChild(iframe);
    }

    var doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><head><title>Print Packing List</title>');
    doc.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    doc.write('<style type="text/css">');
    doc.write('@page { size: portrait; margin: 6mm 6mm; }');
    doc.write('@media print {');
    doc.write('  html, body { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }');
    doc.write('  .hidden-print { display: none !important; }');
    doc.write('  table { width: 100% !important; page-break-inside: auto; }');
    doc.write('  tr { page-break-inside: avoid; page-break-after: auto; }');
    doc.write('}');
    doc.write('html, body { width: 100%; max-width: 780px; margin: 0 auto; padding: 5px; box-sizing: border-box; font-family: "Calibri", Arial, sans-serif; font-size:' + fontSize + 'px; letter-spacing: 0px; color: #000; }');
    doc.write('table { width: 100%; border-spacing: 0; border-collapse: collapse; box-sizing: border-box; }');
    doc.write('th, td { padding: 3px 4px; font-size:' + fontSize + 'px; vertical-align: middle; box-sizing: border-box; }');
    doc.write('th { text-align: center; }');
    doc.write('.piece-cell { font-size: ' + pieceFontSize + 'px !important; font-weight: bold !important; text-align: center !important; padding: 3px 1px !important; line-height: 1.2 !important; white-space: nowrap !important; }');
    doc.write('.piece-cell.empty { background-color: transparent !important; }');
    doc.write('</style>');
    doc.write('</head><body>');
    doc.write(divContentFinals);
    doc.write('</body></html>');
    doc.close();

    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }, 250);
}

$(document).on('input change', '#SizeText', function() {
    var val = $(this).val();
    $('#packingList th, #packingList td').not('.piece-cell').css('font-size', val + 'px');
});

$(document).on('input change', '#SizePieceText', function() {
    var val = $(this).val();
    $('#packingList .piece-cell').css('font-size', val + 'px');
});