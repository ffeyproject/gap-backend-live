function siapKirim(event){
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
                    content: 'Seting siap kirim berhasil',
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

function stock(event){
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