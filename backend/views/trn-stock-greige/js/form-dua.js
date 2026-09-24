function calculateTotalPanjang() {
    var totalPanjang = 0;
    var gradeTotals = {};

    jQuery(".dynamicform_wrapper .item").each(function() {
        var $row = jQuery(this);
        var $qtyInput = $row.find(".panjang_unit");
        var val = parseFloat($qtyInput.val());

        var $gradeSelect = $row.find(".grade_select, select");
        var gradeText = $gradeSelect.find("option:selected").text().trim();
        var gradeVal = $gradeSelect.val();

        if (!isNaN(val) && val > 0) {
            totalPanjang += val;
            var label = (gradeVal && gradeText && gradeText !== '--') ? gradeText : 'Lain';
            if (!gradeTotals[label]) {
                gradeTotals[label] = 0;
            }
            gradeTotals[label] += val;
        }
    });

    var formattedTotal = Math.round(totalPanjang * 100) / 100;
    $("#TotalLength").html(formattedTotal);

    var breakdownParts = [];
    for (var g in gradeTotals) {
        if (gradeTotals.hasOwnProperty(g)) {
            var gVal = Math.round(gradeTotals[g] * 100) / 100;
            breakdownParts.push('<span class="label label-default" style="font-size: 13px; font-weight: normal; margin-right: 5px; color: #333; background: #e8e8e8; border: 1px solid #ccc;">' + g + ': <strong>' + gVal + '</strong></span>');
        }
    }

    if (breakdownParts.length > 0) {
        $("#GradeBreakdown").html('( ' + breakdownParts.join(' ') + ' )').show();
    } else {
        $("#GradeBreakdown").html('').hide();
    }
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

jQuery(document).on("input keyup change", ".dynamicform_wrapper .panjang_unit, .dynamicform_wrapper select", function() {
    calculateTotalPanjang();
});

calculateTotalPanjang();
