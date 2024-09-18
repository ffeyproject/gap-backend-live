$(ajaxModalId).on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);
    var title = button.data('title');
    var href = button.attr('href');
    modal.find('.modal-title').html(title);
    modal.find('.modal-body').html('<div class="ajax-modal-loader"></div>');

    $.get(href).done(function(result){
        modal.find('.modal-body').html(result);
    }).fail(function(xhr, status, error){
        try {
            errorMsgToJson = jQuery.parseJSON(xhr.responseText);
            modal.find('.modal-body').html(
                '<div class="text-danger">' +
                '<p class="lead">'+error+'</p>' +
                errorMsgToJson.message +
                '</div>'
            );
        } catch(err){
            modal.find('.modal-body').html(
                '<div class="text-danger">' +
                '<p class="lead">'+error+'</p>' +
                xhr.responseText +
                '</div>'
            );
        }
    });
});

// Prevent bootstrap dialog from blocking focusin
$(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
    }
});

/*
* Mengatasi masalah Tinymce yang tidak berfungsi saat modal dibuka keduakalinya.
* Hapus instance tinymce setiap kali modal ditutup
* */
$(ajaxModalId).on('hidden.bs.modal',function() {
    if(window.tinyMCE !== undefined && tinyMCE.editors.length){
        for(e in tinyMCE.editors){
            tinyMCE.editors[e].destroy();
        }
        //tinyMCE.editors=[];
    }
});