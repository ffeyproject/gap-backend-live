const formatterNumber = new Intl.NumberFormat("ID-id", {
  style: "decimal",
  minimumFractionDigits: 0,
  maximumFractionDigits: 8,
});

var itemTable = $("#InspectingItemTable").DataTable({
  data: inspectingItems,
  columns: [
    { data: "item_id", visible: false },
    {
      data: function (row, type, set) {
        return "-";
      },
    },
    { data: "gradeLabel" },
    { data: "ukuran" },
    { data: "join_piece" },
    { data: "lot_no" },
    { data: "defect" },
    { data: "keterangan" },
    // { "data": "qr_code" },
    {
      data: "qr_code",
      render: function (data, type, row) {
        if (data) {
          return '<i class="fa fa-qrcode"></i>';
        } else {
          return "";
        }
      },
    },
    {
      data: function (row, type, set) {
        return '<button class="btn btn-xs btn-warning editItemData"><i class="fa fa-edit"></i></button> <button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';
      },
    },
  ],
  ordering: false,
  responsive: true,
  paging: false,
  searching: false,
  info: false,
  columnDefs: [
    {
      render: function (data, type, row) {
        return formatterNumber.format(data);
      },
      targets: [3],
    },
  ],
  rowCallback: function (row, data, index) {
    $("td", row)
      .eq(0)
      .html(index + 1);
  },
});

var deletedItemIds = [];

$("#InspectingItemTable tbody").on(
  "click",
  "button.removeItemData",
  function () {
    var row = itemTable.row($(this).parents("tr"));
    // Get the data for the current row
    var rowData = row.data();
    if (rowData.qr_code) {
      alert("Tidak bisa menghapus data, karena qr sudah di cetak!");
    } else {
      row.remove().draw(false);
      deletedItemIds.push(rowData.item_id);
      $("#ItemCounter").html(itemTable.rows().data().length);
    }
  }
);

$("#InspectingItemTable tbody").on("click", "button.editItemData", function () {
  let row = itemTable.row($(this).parents("tr"));
  let rowNumber = row.index(); //for check the index of row that you want to edit in table
  let data = row.data();
  let gradeOld = data.grade;
  let qr_code = data.qr_code;

  // value edit
  let jp_value = data.join_piece != null ? data.join_piece : "";
  let d_value = data.defect != null ? data.defect : "";
  let ln_value = data.lot_no != null ? data.lot_no : "";
  let u_value = data.ukuran != null ? data.ukuran : "";
  console.log(data);

  $.confirm({
    title: "Edit!",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group"><label>Grade</label><select id="optionGrade" class="editGrade form-control"><option value="7">Grade A+</option> <option value="8">Grade A*</option> <option value="3">Grade C</option> <option value="4">Piece Kecil</option> <option value="5">Sample</option> <option value="1">Grade A</option> <option value="2">Grade B</option> <option value="9">Putih</option></select></div>' +
      '<div class="form-group"><label>Ukuran</label><input type="text" class="editUkuran form-control" value="' +
      u_value +
      '"/></div>' +
      '<div class="form-group"><label>Join Piece</label><input type="text" class="editJoinPiece form-control" value="' +
      jp_value +
      '"/></div>' +
      '<div class="form-group"><label>Lot No</label><input type="text" class="editLotNo form-control" value="' +
      ln_value +
      '"/></div>' +
      '<div class="form-group"><label>Defect</label><input type="text" class="editDefect form-control" value="' +
      d_value +
      '"/></div>' +
      '<div class="form-group"><label>Keterangan</label><input type="text" class="editKeterangan form-control" value="' +
      data.keterangan +
      '"/></div>' +
      "</form>",
    buttons: {
      formSubmit: {
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          let grade = $("#optionGrade").children("option:selected").val();
          let gradeLabel = $("#optionGrade").children("option:selected").text();
          let defect = this.$content.find(".editDefect").val();
          let lot_no = this.$content.find(".editLotNo").val();
          let ukuran = this.$content.find(".editUkuran").val();
          let joinPiece = this.$content.find(".editJoinPiece").val();
          let keterangan = this.$content.find(".editKeterangan").val();

          if (!grade) {
            $.alert("Grade harus diisi.");
            return false;
          }
          if (!ukuran) {
            $.alert("Ukuran harus diisi.");
            return false;
          }

          //console.log({grade: grade, gradeLabel: gradeLabel, ukuran: ukuran, join_piece: joinPiece, keterangan: keterangan});
          itemTable
            .row(rowNumber)
            .data({
              item_id: data.item_id,
              grade: grade,
              gradeLabel: gradeLabel,
              ukuran: ukuran,
              join_piece: joinPiece,
              lot_no: lot_no,
              defect: defect,
              keterangan: keterangan,
              qr_code: qr_code,
            })
            .draw(false);
        },
      },
      cancel: function () {
        //close
      },
    },
    onContentReady: function () {
      // bind to events
      var jc = this;

      this.$content
        .find('option[value="' + gradeOld + '"]')
        .attr("selected", "selected");

      this.$content.find("form").on("submit", function (e) {
        // if the user submits the form by pressing enter in the field.
        e.preventDefault();
        jc.$$formSubmit.trigger("click"); // reference the button and click it
      });
    },
  });
});

$("#InspectingFormHeader").on("afterInit", function (e) {
  kartuProsesIdOnSelect = function (e) {
    let data = e.params.data;

    let jenisProses = $("#InspectingFormHeader").yiiActiveForm(
      "find",
      "inspectingheaderform-jenis_order"
    ).value;
    switch (jenisProses) {
      case "dyeing":
        jenisProses = "dyeing";
        break;
      case "printing":
        jenisProses = "printing";
        break;
    }

    $.get(kpUrl, { q: jenisProses, id: data.id })
      .done(function (data, textStatus, jqXHR) {
        kpModel = data;
        implementData();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        let msg = textStatus;
        if (jqXHR.responseJSON) {
          msg = jqXHR.responseJSON.message;
        }
        $.alert({
          title: errorThrown,
          content: msg,
        });
      })
      .always(function (data, textStatus, jqXHR) {});
  };

  $("#InspectingFormHeader").on("beforeSubmit", function () {
    var $yiiform = $(this);
    let deletedItems = deletedItemIds;
    let dataItems = itemTable.rows().data().toArray();

    console.log(dataItems);

    if (dataItems.length < 1) {
      $.alert({
        title: "Gagal!",
        content: "Tidak ada item untuk disimpan.!",
      });
    } else {
      $.blockUI();

      let formData = $yiiform.serializeArray();
      formData.push(
        { name: "items", value: JSON.stringify(dataItems) },
        { name: "deletedItems", value: JSON.stringify(deletedItems) }
      );

      $.ajax({
        type: $yiiform.attr("method"),
        url: $yiiform.attr("action"),
        data: formData,
        beforeSend: function (jqXHR, settings) {},
        success: function (data, textStatus, jqXHR) {
          console.log(data);
          if (data.success) {
            window.location.replace(data.redirect);
          } else if (data.validation) {
            // server validation failed
            $yiiform.yiiActiveForm("updateMessages", data.validation, true); // renders validation messages at appropriate places
          } else {
            $.alert({
              title: "Gagal!",
              content: "incorrect server response!",
            });
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          //console.log(jqXHR);
          let msg = textStatus;

          if (jqXHR.responseJSON) {
            msg = jqXHR.responseJSON.message;
          }

          $.alert({
            title: errorThrown,
            content: msg,
          });
        },
        complete: function () {
          $.unblockUI();
        },
      });
    }

    return false;
  });
});

function implementData() {
  let tipeKontrak = "Lokal";
  if (kpModel.wo.mo.sc.tipe_kontrak === 2) {
    tipeKontrak = "Export";
  }

  $("#BuyerName").html(kpModel.wo.mo.sc.cust.name);
  $("#NoWo").html(kpModel.wo.no);
  $("#TipeKontrak").html(tipeKontrak);
  $("#PieceLength").html(kpModel.wo.mo.piece_length);
  $("#Motif").html(kpModel.wo.greige.nama_kain);
  $("#Kombinasi").html(kpModel.woColor.moColor.color);
  $("#Stamping").html(kpModel.wo.mo.face_stamping);
  $("#Design").html(kpModel.wo.mo.design === null ? "-" : kpModel.wo.mo.design);
}

function resetData() {
  $("#BuyerName").html("-");
  $("#NoWo").html("-");
  $("#TipeKontrak").html("-");
  $("#PieceLength").html("-");
  $("#Motif").html("-");
  $("#Kombinasi").html("-");
  $("#Stamping").html("-");
  $("#Design").html("-");
}

$("#InspectingFormItem").on("afterInit", function (e) {
  $("#InspectingFormItem").on("beforeSubmit", function () {
    let data = {
      item_id: 0,
      grade: $("#inspectingitemsform-grade").select2("data")[0].id,
      gradeLabel: $("#inspectingitemsform-grade").select2("data")[0].text,
      defect: $("#inspectingitemsform-defect").val(),
      lot_no: $("#inspectingitemsform-lot_no").val(),
      ukuran: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingitemsform-ukuran"
      ).value,
      join_piece: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingitemsform-join_piece"
      ).value,
      keterangan: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingitemsform-keterangan"
      ).value,
      qr_code: 0,
    };
    itemTable.row.add(data).draw(false);

    $("#ItemCounter").html(itemTable.rows().data().length);

    $("#InspectingFormItem").get(0).reset();

    $("#inspectingitemsform-ukuran").focus();

    return false; // prevent default form submission
  });
});

$("#BtnSubmitForm").click(function () {
  $("#InspectingFormHeader").submit();
});
