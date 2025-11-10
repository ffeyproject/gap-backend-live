const formatterNumber = new Intl.NumberFormat("ID-id", {
  style: "decimal",
  minimumFractionDigits: 0,
  maximumFractionDigits: 8,
});

// Fungsi ambil nomor urut otomatis
function getNextNoUrutFromTable() {
  let allData = itemTable.rows().data().toArray();
  if (allData.length === 0) return 1;
  let maxNoUrut = Math.max.apply(
    Math,
    allData.map((it) => parseInt(it.no_urut || 0) || 0)
  );
  return maxNoUrut + 1;
}

// Inisialisasi DataTable
var itemTable = $("#InspectingItemTable").DataTable({
  data: inspectingItems,
  columns: [
    { data: null, title: "No" },
    { data: "no_urut", title: "No Urut" },
    { data: "gradeLabel", title: "Grade" },
    { data: "qty", title: "Ukuran" },
    { data: "join_piece", title: "Join Piece" },
    { data: "lot_no", title: "No Lot" },
    { data: "defect", title: "Defect" },
    { data: "note", title: "Keterangan" },
    {
      data: "qr_code",
      title: "QR",
      render: function (data) {
        return data ? '<i class="fa fa-qrcode"></i>' : "";
      },
    },
    {
      data: null,
      title: "Action",
      render: function () {
        return `
          <button class="btn btn-xs btn-warning editItemData"><i class="fa fa-edit"></i></button>
          <button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>`;
      },
    },
  ],
  ordering: false,
  responsive: true,
  paging: false,
  searching: false,
  info: false,
  rowCallback: function (row, data, index) {
    $("td", row)
      .eq(0)
      .html(index + 1);
  },
});

var deletedItemIds = [];

// Hapus item
$("#InspectingItemTable tbody").on(
  "click",
  "button.removeItemData",
  function () {
    var row = itemTable.row($(this).parents("tr"));
    var rowData = row.data();
    if (rowData.qr_code) {
      alert("Tidak bisa menghapus data, karena QR sudah dicetak!");
    } else {
      row.remove().draw(false);
      deletedItemIds.push(rowData.id);
      $("#ItemCounter").html(itemTable.rows().data().length);
    }
  }
);

// Edit item
$("#InspectingItemTable tbody").on("click", "button.editItemData", function () {
  let row = itemTable.row($(this).parents("tr"));
  let data = row.data();
  let rowIndex = row.index();

  $.confirm({
    title: "Edit Item",
    content: `
      <form class="formName">
        <div class="form-group"><label>No Urut</label>
          <input type="number" class="editNoUrut form-control" value="${
            data.no_urut ?? ""
          }" placeholder="Isi atau biarkan kosong (otomatis)">
        </div>
        <div class="form-group"><label>Grade</label>
          <select id="optionGrade" class="editGrade form-control">
            <option value="1">Grade A</option>
            <option value="2">Grade B</option>
            <option value="3">Grade C</option>
            <option value="4">Piece Kecil</option>
            <option value="5">Sample</option>
            <option value="7">Grade A+</option>
            <option value="8">Grade A*</option>
            <option value="9">Grade Putih</option>
          </select>
        </div>
        <div class="form-group"><label>Ukuran</label><input type="text" class="editUkuran form-control" value="${
          data.qty ?? ""
        }"/></div>
        <div class="form-group"><label>Join Piece</label><input type="text" class="editJoinPiece form-control" value="${
          data.join_piece ?? ""
        }"/></div>
        <div class="form-group"><label>Lot No</label><input type="text" class="editLotNo form-control" value="${
          data.lot_no ?? ""
        }"/></div>
        <div class="form-group"><label>Defect</label><input type="text" class="editDefect form-control" value="${
          data.defect ?? ""
        }"/></div>
        <div class="form-group"><label>Keterangan</label><input type="text" class="editKeterangan form-control" value="${
          data.note ?? ""
        }"/></div>
      </form>`,
    buttons: {
      submit: {
        text: "Simpan",
        btnClass: "btn-blue",
        action: function () {
          let no_urut = this.$content.find(".editNoUrut").val().trim();
          let grade = $("#optionGrade").val();
          let gradeLabel = $("#optionGrade option:selected").text();
          let qty = this.$content.find(".editUkuran").val().trim();

          if (!grade) return $.alert("Grade harus diisi!");
          if (!qty) return $.alert("Ukuran harus diisi!");

          let joinPiece = this.$content.find(".editJoinPiece").val().trim();
          let lot_no = this.$content.find(".editLotNo").val().trim();
          let defect = this.$content.find(".editDefect").val().trim();
          let note = this.$content.find(".editKeterangan").val().trim();

          itemTable
            .row(rowIndex)
            .data({
              id: data.id,
              no_urut: no_urut || data.no_urut || getNextNoUrutFromTable(),
              grade,
              gradeLabel,
              qty,
              join_piece: joinPiece,
              lot_no,
              defect,
              note,
              qr_code: data.qr_code,
            })
            .draw(false);
        },
      },
      cancel: function () {},
    },
    onContentReady: function () {
      this.$content
        .find(`#optionGrade option[value='${data.grade}']`)
        .attr("selected", "selected");
    },
  });
});

// Submit form header (update)
$("#InspectingFormHeader").on("afterInit", function () {
  $("#InspectingFormHeader").on("beforeSubmit", function () {
    let $yiiform = $(this);
    let dataItems = itemTable.rows().data().toArray();
    if (dataItems.length < 1) {
      $.alert({ title: "Gagal!", content: "Tidak ada item untuk disimpan!" });
      return false;
    }

    $.blockUI();
    let formData = $yiiform.serializeArray();
    formData.push(
      { name: "items", value: JSON.stringify(dataItems) },
      { name: "deletedItems", value: JSON.stringify(deletedItemIds) }
    );

    $.ajax({
      type: $yiiform.attr("method"),
      url: $yiiform.attr("action"),
      data: formData,
      success: function (data) {
        if (data.success) {
          window.location.replace(data.redirect);
        } else if (data.validation) {
          $yiiform.yiiActiveForm("updateMessages", data.validation, true);
        } else {
          $.alert({ title: "Gagal!", content: "Incorrect server response!" });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $.alert({
          title: errorThrown,
          content: jqXHR.responseJSON ? jqXHR.responseJSON.message : textStatus,
        });
      },
      complete: function () {
        $.unblockUI();
      },
    });
    return false;
  });
});

// Tambah item baru
$("#InspectingFormItem").on("afterInit", function () {
  $("#InspectingFormItem").on("beforeSubmit", function () {
    let manualNoUrut = $("#inspectingmklbjitems-no_urut").val().trim();
    let nextNoUrut =
      manualNoUrut !== "" ? parseInt(manualNoUrut) : getNextNoUrutFromTable();

    let data = {
      id: 0,
      no_urut: nextNoUrut,
      grade: $("#inspectingmklbjitems-grade").select2("data")[0].id,
      gradeLabel: $("#inspectingmklbjitems-grade").select2("data")[0].text,
      defect: $("#inspectingmklbjitems-defect").val(),
      lot_no: $("#inspectingmklbjitems-lot_no").val(),
      qty: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingmklbjitems-qty"
      ).value,
      join_piece: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingmklbjitems-join_piece"
      ).value,
      note: $("#InspectingFormItem").yiiActiveForm(
        "find",
        "inspectingmklbjitems-note"
      ).value,
      qr_code: 0,
    };

    itemTable.row.add(data).draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);

    $("#InspectingFormItem").get(0).reset();
    $("#inspectingmklbjitems-no_urut").val(getNextNoUrutFromTable());
    $("#inspectingmklbjitems-qty").focus();

    return false;
  });
});

$("#BtnSubmitForm").click(function () {
  $("#InspectingFormHeader").submit();
});
