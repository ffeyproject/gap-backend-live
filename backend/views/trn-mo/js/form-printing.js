var $moForm = $("#TrnMoFormPrinting");
$moForm.on("afterInit", function (e) {
  //console.log(e);

  /*var nomorWo = "";
    $("#trnmoprintingform-re_wo").keyup(function(){
        nomorWo = this.value;
        console.log(nomorWo);
    });*/

  $("#CheckBtMoReWo").click(function (e) {
    let noWo = $("#trnmoprintingform-re_wo").val();

    if (!noWo) {
      $moForm.yiiActiveForm("updateAttribute", "trnmoprintingform-re_wo", [
        "Nomor WO tidak boleh kosong.",
      ]);
    } else {
      $moForm.yiiActiveForm("updateAttribute", "trnmoprintingform-re_wo", "");

      $.ajax({
        method: "POST",
        beforeSend: function (jqXHR, settings) {
          $.blockUI({
            message: "<h1>Processing</h1>",
            css: { border: "3px solid #a00" },
          });
        },
        data: { reWo: noWo },
        url: reWoUrl,
        error: function (jqXHR, textStatus, errorThrown) {
          var errorObj;
          try {
            errorObj = jQuery.parseJSON(jqXHR.responseText);
            if (typeof errorObj != "object") {
              errorObj = { name: "Error", message: jqXHR.responseText };
            }
          } catch (e) {
            errorObj = { name: "Error", message: jqXHR.responseText };
          }

          $.unblockUI();

          $.alert({
            title: errorObj.name,
            content: errorObj.message,
          });
        },
        success: function (data) {
          $.unblockUI();

          console.log(data);

          $("#trnmoprintingform-album").val(data["album"]);
          $("#trnmoprintingform-arsip").val(data["arsip"]);
          $("#trnmoprintingform-article").val(data["article"]);
          $("#trnmoprintingform-block_size").val(data["block_size"]);
          $("#trnmoprintingform-border_size").val(data["border_size"]);
          $("#trnmoprintingform-design").val(data["design"]);
          $("#trnmoprintingform-face_stamping").val(data["face_stamping"]);
          $("#trnmoprintingform-foil").val(data["foil"]);
          $("#trnmoprintingform-folder").val(data["folder"]);
          $("#trnmoprintingform-hanger").val(data["hanger"]);
          $("#trnmoprintingform-heat_cut").val(data["heat_cut"] ? 1 : 0);
          $("#trnmoprintingform-jet_black").val(data["jet_black"] ? 1 : 0);
          $("#trnmoprintingform-joint").val(data["joint"] ? 1 : 0);
          $("#trnmoprintingform-joint_qty").val(data["joint_qty"]);
          $("#trnmoprintingform-label").val(data["label"]);
          $("#trnmoprintingform-piece_length").val(data["piece_length"]);
          $("#trnmoprintingform-plastic").val(data["plastic"]);
          $("#trnmoprintingform-selvedge_continues").val(
            data["selvedge_continues"]
          );
          $("#trnmoprintingform-selvedge_stamping").val(
            data["selvedge_stamping"]
          );
          $("#trnmoprintingform-shipping_method").val(data["shipping_method"]);
          $("#trnmoprintingform-shipping_sorting").val(
            data["shipping_sorting"]
          );
          $("#trnmoprintingform-side_band").val(data["side_band"]);
          $("#trnmoprintingform-strike_off").val(data["strike_off"]);
          $("#trnmoprintingform-tag").val(data["tag"]);
          $("#trnmoprintingform-packing_method").val(data["packing_method"]);
          $("#trnmoprintingform-handling").val(data["handling"]);
          $("#trnmoprintingform-no_po").val(data["no_po"]);

          /*$.alert({
                        title: "Berhasil",
                        content: "MO telah close.",
                        buttons: {
                            ok: function () {
                                location.reload();
                            }
                        }
                    });*/
        },
      });
    }
  });
});
