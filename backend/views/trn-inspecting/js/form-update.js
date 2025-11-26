const formatterNumber = new Intl.NumberFormat("ID-id", {
  style: "decimal",
  minimumFractionDigits: 0,
  maximumFractionDigits: 8,
});

// ========================
//  Inisialisasi DataTable
// ========================
var itemTable = $("#InspectingItemTable").DataTable({
  data: inspectingItems, // data dari PHP
  columns: [
    {
      data: null,
      render: function (data, type, row, meta) {
        return meta.row + 1; // Kolom "No"
      },
    },
    { data: "no_urut" },
    { data: "gradeLabel" },
    { data: "ukuran" },
    { data: "join_piece" },
    { data: "lot_no" },
    { data: "defect" },
    { data: "keterangan" },
    {
      data: null,
      render: function () {
        return `
          <button class="btn btn-xs btn-warning editItemData">
            <i class="fa fa-edit"></i>
          </button>
          <button class="btn btn-xs btn-danger removeItemData">
            <i class="fa fa-trash"></i>
          </button>`;
      },
    },
  ],
  ordering: false,
  responsive: true,
  paging: false,
  searching: false,
  info: false,
});

// ========================
//  Fungsi helper No Urut
// ========================
function getNextNoUrutFromTable() {
  let allData = itemTable.rows().data().toArray();
  if (allData.length === 0) return 1;
  let maxNoUrut = Math.max.apply(
    Math,
    allData.map((it) => parseInt(it.no_urut || 0) || 0)
  );
  return maxNoUrut + 1;
}

// ========================
//  Hapus item
// ========================
$("#InspectingItemTable tbody").on(
  "click",
  "button.removeItemData",
  function () {
    itemTable.row($(this).parents("tr")).remove().draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);
  }
);

// ========================
//  Edit item
// ========================
$("#InspectingItemTable tbody").on("click", "button.editItemData", function () {
  let row = itemTable.row($(this).parents("tr"));
  let rowNumber = row.index();
  let data = row.data();
  let gradeOld = data.grade;
  let no_urut_value = data.no_urut || rowNumber + 1;

  $.confirm({
    title: "Edit Item",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group"><label>No Urut</label><input type="text" class="editNoUrut form-control" value="' +
      no_urut_value +
      '"/></div>' +
      '<div class="form-group"><label>Grade</label><select id="optionGrade" class="editGrade form-control">' +
      '<option value="7">Grade A+</option><option value="8">Grade A*</option><option value="3">Grade C</option><option value="4">Piece Kecil</option><option value="5">Sample</option><option value="1">Grade A</option><option value="2">Grade B</option><option value="10">Grade D</option>' +
      "</select></div>" +
      '<div class="form-group"><label>Ukuran</label><input type="text" class="editUkuran form-control" value="' +
      (data.ukuran || "") +
      '"/></div>' +
      '<div class="form-group"><label>Join Piece</label><input type="text" class="editJoinPiece form-control" value="' +
      (data.join_piece || "") +
      '"/></div>' +
      '<div class="form-group"><label>Lot No</label><input type="text" class="editLotNo form-control" value="' +
      (data.lot_no || "") +
      '"/></div>' +
      '<div class="form-group"><label>Defect</label><input type="text" class="editDefect form-control" value="' +
      (data.defect || "") +
      '"/></div>' +
      '<div class="form-group"><label>Keterangan</label><input type="text" class="editKeterangan form-control" value="' +
      (data.keterangan || "") +
      '"/></div>' +
      "</form>",
    buttons: {
      formSubmit: {
        text: "Simpan",
        btnClass: "btn-blue",
        action: function () {
          let no_urut = this.$content.find(".editNoUrut").val();
          let grade = $("#optionGrade").val();
          let gradeLabel = $("#optionGrade option:selected").text();
          let defect = this.$content.find(".editDefect").val();
          let lot_no = this.$content.find(".editLotNo").val();
          let ukuran = this.$content.find(".editUkuran").val();
          let joinPiece = this.$content.find(".editJoinPiece").val();
          let keterangan = this.$content.find(".editKeterangan").val();

          if (!grade || !ukuran) {
            $.alert("Grade dan Ukuran wajib diisi!");
            return false;
          }

          itemTable
            .row(rowNumber)
            .data({
              id: data.id, // pertahankan ID
              no_urut: parseInt(no_urut) || rowNumber + 1,
              grade: grade,
              gradeLabel: gradeLabel,
              ukuran: ukuran,
              join_piece: joinPiece,
              lot_no: lot_no,
              defect: defect,
              keterangan: keterangan,
            })
            .draw(false);
        },
      },
      cancel: function () {},
    },
    onContentReady: function () {
      var jc = this;
      this.$content
        .find('option[value="' + gradeOld + '"]')
        .attr("selected", "selected");
      this.$content.find("form").on("submit", function (e) {
        e.preventDefault();
        jc.$$formSubmit.trigger("click");
      });
    },
  });
});

// ========================
//  Tambah item baru (revisi agar no_urut manual tidak tertimpa)
// ========================
$("#InspectingFormItem").on("afterInit", function () {
  $("#InspectingFormItem").on("beforeSubmit", function () {
    // Ambil isi dari input manual no_urut
    let manualNoUrut = $("#inspectingitemsform-no_urut").val().trim();

    // Jika user isi manual, pakai itu; kalau kosong, auto generate
    let nextNoUrut =
      manualNoUrut !== "" ? parseInt(manualNoUrut) : getNextNoUrutFromTable();

    let data = {
      id: null, // item baru
      no_urut: nextNoUrut,
      grade: $("#inspectingitemsform-grade").select2("data")[0].id,
      gradeLabel: $("#inspectingitemsform-grade").select2("data")[0].text,
      defect: $("#inspectingitemsform-defect").val(),
      lot_no: $("#inspectingitemsform-lot_no").val(),
      ukuran: $("#inspectingitemsform-ukuran").val(),
      join_piece: $("#inspectingitemsform-join_piece").val(),
      keterangan: $("#inspectingitemsform-keterangan").val(),
    };

    itemTable.row.add(data).draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);

    // Reset form input
    $("#InspectingFormItem").get(0).reset();

    // Isi otomatis no urut berikutnya (hanya jika user tidak isi manual)
    $("#inspectingitemsform-no_urut").val(getNextNoUrutFromTable());
    $("#inspectingitemsform-ukuran").focus();

    return false;
  });
});

// ========================
//  Submit Header (AJAX)
// ========================
$("#InspectingFormHeader").on("afterInit", function () {
  $("#InspectingFormHeader").on("beforeSubmit", function () {
    var $yiiform = $(this);
    let dataItems = itemTable.rows().data().toArray();

    if (dataItems.length < 1) {
      $.alert({
        title: "Gagal!",
        content: "Tidak ada item untuk disimpan!",
      });
      return false;
    }

    $.blockUI();
    let formData = $yiiform.serializeArray();
    formData.push({ name: "items", value: JSON.stringify(dataItems) });

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
          $.alert({
            title: "Gagal!",
            content: "incorrect server response!",
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        let msg = jqXHR.responseJSON ? jqXHR.responseJSON.message : textStatus;
        $.alert({
          title: errorThrown,
          content: msg,
        });
      },
      complete: function () {
        $.unblockUI();
      },
    });

    return false;
  });
});

// ========================
//  Tombol Save utama
// ========================
$("#BtnSubmitForm").click(function () {
  $("#InspectingFormHeader").submit();
});
