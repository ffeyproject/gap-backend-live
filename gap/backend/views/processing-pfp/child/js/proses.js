function setDateInput(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');
    var parentEl = button.parent();

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
                '<div class="input-group">' +
                    '<div class="input-group-addon">' +
                        '<span class="glyphicon glyphicon-th"></span>' +
                    '</div>' +
                    '<input type="text" class="form-control kartu-proses-tanggal datepicker" readonly>' +
                '</div>' +
                '<div class="text-danger kartu-proses-tanggal-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.kartu-proses-tanggal').val();
                    if(!ctn){
                        this.$content.find('.kartu-proses-tanggal-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label, parentEl);
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

            $(".datepicker").datepicker({
                beforeShow: function() {
                    setTimeout(function(){
                        $('.ui-datepicker').css('z-index', 99999999999999);
                    }, 0);
                },
                dateFormat: "yy-mm-dd",
            });
        }
    });
}

function setTimeInput(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');
    var parentEl = button.parent();
    //console.log(flag);console.log(href);

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<input type="text" class="form-control kartu-proses-time">' +
            '<div class="hint-block">Contoh format pengisian: 18:00</div>' +
            '<div class="text-danger kartu-proses-time-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.kartu-proses-time').val();
                    if(!ctn){
                        this.$content.find('.kartu-proses-time-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }
                    ctn = ctn + ":00";
                    let re = /(?:[01]\d|2[0123]):(?:[012345]\d):(?:[012345]\d)/gm; //18:00:00
                    if(!ctn.match(re)){
                        this.$content.find('.kartu-proses-time-hint').html('Format Waktu '+label+ ' tidak valid.');
                        return false;
                    }

                    postingProses(href, ctn, label, parentEl);
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

function setTextInput(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');
    var parentEl = button.parent();

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<input type="text" class="form-control kartu-proses-text">' +
            '<div class="text-danger text-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.kartu-proses-text').val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label, parentEl);
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

function setProsesUlang(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<textarea class="form-control kartu-proses-textarea" aria-describedby="helpBlock"></textarea>' +
            '<div class="text-danger kartu-proses-textarea-hint"></div>' +
            '<span id="helpBlock" class="help-block">Harap teliti sebelum melakukan pengulangan proses karena tidak dapat dikembalikan lagi.</span>' +
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
                                content: "Proses Ulang Berhasil.",
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

//POSTING--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function postingProses(href, ctn, label, parentEl) {
    console.log(href);console.log(ctn);

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
            parentEl.html(data);

            /*$.alert({
                title: "Berhasil",
                content: "Proses " + label + " telah diinput.",
                buttons: {
                    ok: function () {
                        location.reload();
                    }
                }
            });*/
        }
    });
}
//POSTING--------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*
function setNumberInput(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<input type="number" class="form-control kartu-proses-text">' +
            '<div class="text-danger text-hint"></div>' +
            '</div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find('.kartu-proses-text').val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionOne(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
                '<input type="radio" name="radioOptionsOne" id="radioOne1" value="Banyak"> Banyak' +
            '</label>' +
            '<label class="radio-inline">' +
                '<input type="radio" name="radioOptionsOne" id="radioOne2" value="Sedang"> Sedang' +
            '</label>' +
            '<label class="radio-inline">' +
                '<input type="radio" name="radioOptionsOne" id="radioOne3" value="Sedikit"> Sedikit' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsOne" id="radioOne4" value="Tidak Ada"> Tidak Ada' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsOne']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionTwo(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsTwo" id="radioTwo1" value="Bersih"> Bersih' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsTwo" id="radioTwo2" value="Cangkengan"> Cangkengan' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsTwo" id="radioTwo3" value="Kotor"> Kotor' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsTwo" id="radioTwo4" value="Tidak Ada"> Tidak Ada' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsTwo']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionThree(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsThree" id="radioThree1" value="Bagus"> Bagus' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsThree" id="radioThree2" value="Sebagian Variasi"> Sebagian Variasi' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsThree" id="radioThree3" value="Variasi"> Variasi' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsThree']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionFour(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFour" id="radioFour1" value="Bagus"> Bagus' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFour" id="radioFour2" value="Sebagian Kotor"> Sebagian Kotor' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFour" id="radioFour3" value="Kotor"> Kotor' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsFour']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionFive(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFive" id="radioFive1" value="Bagus"> Bagus' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFive" id="radioFive2" value="Sebagian Kotor"> Sebagian Kotor' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsFive" id="radioFive3" value="Kotor"> Kotor' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsFive']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setOptionSix(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsSix" id="radioSix1" value="Bersih"> Bersih' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsSix" id="radioSix2" value="Sebagian Kotor"> Sebagian Kotor' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsSix" id="radioSix3" value="Kotor"> Kotor' +
            '</label>' +
            '<label class="radio-inline">' +
            '<input type="radio" name="radioOptionsSix" id="radioSix3" value="Cuci 2 Kali"> Cuci 2 Kali' +
            '</label>' +
            '<div class="text-danger text-hint"></div>' +
            '</form>'
        ,
        buttons: {
            submit: {
                text: 'Submit',
                btnClass : 'btn-blue',
                action: function(){
                    var ctn = this.$content.find("input[name='radioOptionsSix']:checked").val();
                    if(!ctn){
                        this.$content.find('.text-hint').html('Harap masukan '+label+ '!!');
                        return false;
                    }

                    postingProses(href, ctn, label);
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

function setTextarea(event, label){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    $.confirm({
        title: label,
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<textarea class="form-control kartu-proses-textarea"></textarea>' +
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

                    postingProses(href, ctn, label);
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
*/