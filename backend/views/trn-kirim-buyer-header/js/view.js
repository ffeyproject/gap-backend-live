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
    var fontSize = document.getElementById("SizeText").value;

    var divContents = document.getElementById(div).innerHTML;
    //var a = window.open('', '', 'height=500, width=500');
    var a = window.open('', '');
    a.document.write('<html>');
    a.document.write('<head>');
    a.document.write('<style type="text/css">');
    a.document.write('body{font-family: "Calibri"; font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 2px;} th, td {padding: 0.5em 1em;}');
    //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
    a.document.write('</style>');
    a.document.write('</head>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}

function printDivPL(div) {
    var fontSize = document.getElementById("SizeText").value;

    // Capture input values
    var balInputs = document.getElementsByClassName("balInput");
    var balValues = [];
    for (var i = 0; i < balInputs.length; i++) {
        balValues.push(balInputs[i].value);
    }

    // console.log(balValues);

    var divContents = document.getElementById(div).innerHTML;
    // console.log(divContents);

    // Create a temporary div element
    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = divContents;

    // Select all elements with class 'balInput' within the temporary div
    var tempBalInputs = tempDiv.querySelectorAll('.balInput');
    // console.log(tempBalInputs);

    // Iterate through the balInputs and replace them with span elements
    tempBalInputs.forEach(function(tempBalInput, index) {
        var spanElement = document.createElement('span');
        spanElement.textContent = balValues[index];
        tempBalInput.parentNode.replaceChild(spanElement, tempBalInput);
    });

    var divContentFinals = tempDiv.innerHTML;

    // console.log(divContentFinals);

    //var a = window.open('', '', 'height=500, width=500');
    var a = window.open('', '');
    a.document.write('<html>');
    a.document.write('<head>');
    a.document.write('<style type="text/css">');
    a.document.write('body{font-family: "Calibri"; font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 2px;} th, td {padding: 0.5em 1em;}');
    //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
    a.document.write('</style>');
    a.document.write('</head>');
    a.document.write('<body>');
    a.document.write(divContentFinals);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}