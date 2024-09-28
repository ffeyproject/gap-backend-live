jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

$( "#BtnKonversi" ).click(function() {
    let yd = $("#inputYard").val();
    if (!yd.trim()) {
        $.alert({
            title: "Invalid",
            content: "Silahkan isi panjang Yard"
        });
    }else{
        let mToYd = yd * yToM;
        $("#inMeterRes").val(mToYd);
        /*$.alert({
            title: yd,
            content: yd * yToM
        });*/
    }
});

$('#BtnAddMultiple').on('click', function() {
    var qty = parseInt($('#qty-add-multiple').val());
    var pcs = parseInt($('#pcs-add-multiple').val());

    if (!qty || !pcs) {
        alert('Please enter valid quantity and pcs.');
        return;
    }

    var isEmpty = false;
    if ($('.dynamicform_wrapper .container-items tr.item').length === 1) {
        var firstQty = $('.dynamicform_wrapper .container-items tr.item:first input').val();

        if (!firstQty) {
            isEmpty = true;
            // $('.dynamicform_wrapper .container-items tr.item:first').remove();
        }   
    }

    // Loop untuk menambahkan item sesuai jumlah pcs
    for (var i = 0; i < pcs; i++) {
        // Clone row item dari dynamicform
        var clone = $('.dynamicform_wrapper .container-items tr.item:last').clone(true);
        
        // Update index untuk input yang di-clone
        clone.find('input').each(function() {
            var name = $(this).attr('name');
            var newName = name.replace(/\[\d+\]/, '[' + ($('.container-items tr.item').length) + ']');
            $(this).attr('name', newName).val(qty); // Set nilai qty untuk input qty
        });

        // Append row baru ke table
        $('.dynamicform_wrapper .container-items').append(clone);
    }


    if (isEmpty) {
        // Jika baris pertama kosong, hapus baris pertama
        $('.dynamicform_wrapper .container-items tr.item:first').remove();
    }

    updateRowNumbers();

    // Reset input setelah selesai
    $('#qty-add-multiple').val('');
    $('#pcs-add-multiple').val('');
});

function updateRowNumbers() {
    $('.container-items tr.item').each(function(index) {
        $(this).find('.panel-title-address').text(index + 1); // Perbarui nomor baris
    });
}