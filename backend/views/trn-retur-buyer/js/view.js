function printDiv() {
    var fontSize = document.getElementById("SizeText").value;

    var divContents = document.getElementById("GFG").innerHTML;
    //var a = window.open('', '', 'height=500, width=500');
    var a = window.open('', '');
    a.document.write('<html>');
    a.document.write('<head>');
    a.document.write('<style type="text/css">');
    a.document.write('body{font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 2px;} th, td {padding: 0.5em 1em;}');
    //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
    a.document.write('</style>');
    a.document.write('</head>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}

function posting(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: 'Konfirmasi!',
        content:kpOptions,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.QCOpt').val();
                    if(!ctn){
                        this.$content.find('.err-block').html("Harap masukan Piihan!!");
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
                            //console.log(data);
                            $.alert({
                                title: "Berhasil",
                                content: "Posting berhasil.",
                                buttons: {
                                    ok: function () {
                                        //window.location.replace(indexUrl);
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

function setReDyeing(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: "Konfirmasi",
        content: "Anda yakin akan melanjutkan proses ini?.",
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
                        //console.log(data);
                        $.alert({
                            title: "Berhasil",
                            content: "Retur ini akan dilanjutkan dengan proses Re Dyeing.",
                            buttons: {
                                ok: function () {
                                    //window.location.replace(indexUrl);
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            },
            batal: function () {}
        }
    });
}

function setRepair(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: "Konfirmasi",
        content: "Anda yakin akan melanjutkan proses ini?.",
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
                        //console.log(data);
                        $.alert({
                            title: "Berhasil",
                            content: "Retur ini akan dilanjutkan dengan proses Repair.",
                            buttons: {
                                ok: function () {
                                    //window.location.replace(indexUrl);
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            },
            batal: function () {}
        }
    });
}