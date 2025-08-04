var $moForm = $("#TrnMoFormDyeing");
$moForm.on("afterInit", function (e) {
  //console.log(e);

  /*var nomorWo = "";
    $("#trnmodyeingform-re_wo").keyup(function(){
        nomorWo = this.value;
        console.log(nomorWo);
    });*/

  $("#CheckBtMoReWo").click(function (e) {
    let noWo = $("#trnmodyeingform-re_wo").val();

    if (!noWo) {
      $moForm.yiiActiveForm("updateAttribute", "trnmodyeingform-re_wo", [
        "Nomor WO tidak boleh kosong.",
      ]);
    } else {
      $moForm.yiiActiveForm("updateAttribute", "trnmodyeingform-re_wo", "");

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

          $("#trnmodyeingform-album").val(data["album"]);
          $("#trnmodyeingform-arsip").val(data["arsip"]);
          $("#trnmodyeingform-article").val(data["article"]);
          $("#trnmodyeingform-block_size").val(data["block_size"]);
          $("#trnmodyeingform-border_size").val(data["border_size"]);
          $("#trnmodyeingform-design").val(data["design"]);
          $("#trnmodyeingform-face_stamping").val(data["face_stamping"]);
          $("#trnmodyeingform-folder").val(data["folder"]);
          $("#trnmodyeingform-hanger").val(data["hanger"]);
          $("#trnmodyeingform-heat_cut").val(data["heat_cut"] ? 1 : 0);
          $("#trnmodyeingform-jet_black").val(data["jet_black"] ? 1 : 0);
          $("#trnmodyeingform-joint").val(data["joint"] ? 1 : 0);
          $("#trnmodyeingform-joint_qty").val(data["joint_qty"]);
          $("#trnmodyeingform-label").val(data["label"]);
          $("#trnmodyeingform-piece_length").val(data["piece_length"]);
          $("#trnmodyeingform-plastic").val(data["plastic"]);
          $("#trnmodyeingform-selvedge_continues").val(
            data["selvedge_continues"]
          );
          $("#trnmodyeingform-selvedge_stamping").val(
            data["selvedge_stamping"]
          );
          $("#trnmodyeingform-shipping_method").val(data["shipping_method"]);
          $("#trnmodyeingform-shipping_sorting").val(data["shipping_sorting"]);
          $("#trnmodyeingform-side_band").val(data["side_band"]);
          $("#trnmodyeingform-strike_off").val(data["strike_off"]);
          $("#trnmodyeingform-sulam_pinggir").val(data["sulam_pinggir"]);
          $("#trnmodyeingform-no_lab_dip").val(data["sulam_pinggir"]);
          $("#trnmodyeingform-tag").val(data["tag"]);
          $("#trnmodyeingform-packing_method").val(data["packing_method"]);
          $("#trnmodyeingform-handling").val(data["handling"]);
          $("#trnmodyeingform-no_po").val(data["no_po"]);

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
