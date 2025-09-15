jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });

    var totalPanjang = 0;
    jQuery(".dynamicform_wrapper .panjang_unit").each(function(index) {
        var input = jQuery(this);
        //console.log(Number(input.val()));
        totalPanjang += Number(input.val());
    });
    $("#TotalLength").html(totalPanjang);
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1))
    });

    var totalPanjang = 0;
    jQuery(".dynamicform_wrapper .panjang_unit").each(function(index) {
        var input = jQuery(this);
        //console.log(Number(input.val()));
        totalPanjang += Number(input.val());
    });
    $("#TotalLength").html(totalPanjang);
});