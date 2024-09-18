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