function calculateTotalPanjang() {
    var totalPanjang = 0;
    jQuery(".dynamicform_wrapper .panjang_unit").each(function() {
        var val = parseFloat(jQuery(this).val());
        if (!isNaN(val)) {
            totalPanjang += val;
        }
    });
    var formatted = Math.round(totalPanjang * 100) / 100;
    $("#TotalLength").html(formatted);
}

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1));
    });
    calculateTotalPanjang();
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html((index + 1));
    });
    calculateTotalPanjang();
});

jQuery(document).on("input keyup change", ".dynamicform_wrapper .panjang_unit", function() {
    calculateTotalPanjang();
});

calculateTotalPanjang();