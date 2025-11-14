const formatterNumber = new Intl.NumberFormat("ID-id", {
  style: "decimal",
  minimumFractionDigits: 0,
  maximumFractionDigits: 8,
});

// ========================
//  Inisialisasi DataTable
// ========================
var itemTable = $("#InspectingItemTable").DataTable({
  data: inspectingItems,
  columns: [
    {
      data: function () {
        return "-";
      },
    },
    { data: "no_urut" }, // NEW
    { data: "gradeLabel" },
    { data: "ukuran" },
    { data: "join_piece" },
    { data: "lot_no" },
    { data: "defect" },
    { data: "keterangan" },
    {
      data: function () {
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
  columnDefs: [
    {
      render: function (data) {
        return formatterNumber.format(data);
      },
      targets: [3], // kolom ukuran
    },
  ],
  rowCallback: function (row, data, index) {
    $("td", row)
      .eq(0)
      .html(index + 1);
  },
});

// ========================
//  Auto isi no_urut saat load
// ========================
$(document).ready(function () {
  // Ambil nilai max no_urut dari tabel
  let nextNoUrut = getNextNoUrutFromTable();
  $("#inspectingitemsform-no_urut").val(nextNoUrut);
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
    var row = itemTable.row($(this).parents("tr"));

    // Hapus dulu
    row.remove();

    // Reset nomor urut
    itemTable.rows().every(function (index) {
      let d = this.data();
      d.no_urut = index + 1;
      this.data(d);
    });

    itemTable.draw(false);

    // Update counter
    $("#ItemCounter").html(itemTable.rows().data().length);

    // ✔ UPDATE ISIAN FORM NO_URUT OTOMATIS
    $("#inspectingitemsform-no_urut").val(getNextNoUrutFromTable());
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
    title: "Edit!",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group"><label>No Urut</label><input type="text" class="editNoUrut form-control" value="' +
      no_urut_value +
      '"/></div>' +
      '<div class="form-group"><label>Grade</label><select id="optionGrade" class="editGrade form-control">' +
      '<option value="7">Grade A+</option> <option value="8">Grade A*</option> <option value="3">Grade C</option> <option value="4">Piece Kecil</option> <option value="5">Sample</option> <option value="1">Grade A</option> <option value="2">Grade B</option>' +
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
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          let no_urut = this.$content.find(".editNoUrut").val();
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

          itemTable
            .row(rowNumber)
            .data({
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
//  Header form
// ========================
$("#InspectingFormHeader").on("afterInit", function () {
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
      .done(function (data) {
        kpModel = data;
        implementData();
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        let msg = jqXHR.responseJSON ? jqXHR.responseJSON.message : textStatus;
        $.alert({ title: errorThrown, content: msg });
      });
  };

  // submit header form (AJAX)
  $("#InspectingFormHeader").on("beforeSubmit", function () {
    var $yiiform = $(this);
    let dataItems = itemTable.rows().data().toArray();

    if (dataItems.length < 1) {
      $.alert({ title: "Gagal!", content: "Tidak ada item untuk disimpan.!" });
    } else {
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
          let msg = jqXHR.responseJSON
            ? jqXHR.responseJSON.message
            : textStatus;
          $.alert({ title: errorThrown, content: msg });
        },
        complete: function () {
          $.unblockUI();
        },
      });
    }

    return false;
  });
});

// ========================
//  Implement data header
// ========================
function implementData() {
  let tipeKontrak = "Lokal";
  if (kpModel.wo.mo.sc.tipe_kontrak === 2) tipeKontrak = "Export";

  $("#BuyerName").html(kpModel.wo.mo.sc.cust.name);
  $("#NoWo").html(kpModel.wo.no);
  $("#TipeKontrak").html(tipeKontrak);
  $("#PieceLength").html(kpModel.wo.mo.piece_length);
  $("#Motif").html(kpModel.wo.greige.nama_kain);
  $("#Kombinasi").html(kpModel.woColor.moColor.color);
  $("#Stamping").html(kpModel.wo.mo.face_stamping);
  $("#Design").html(kpModel.wo.mo.design || "-");
}

function resetData() {
  $(
    "#BuyerName,#NoWo,#TipeKontrak,#PieceLength,#Motif,#Kombinasi,#Stamping,#Design"
  ).html("-");
}

// ========================
//  Tambah item baru
// ========================
$("#InspectingFormItem").on("afterInit", function () {
  $("#InspectingFormItem").on("beforeSubmit", function () {
    // Ambil input manual jika diisi
    let manualNoUrut = $("#inspectingitemsform-no_urut").val();
    let nextNoUrut =
      manualNoUrut && manualNoUrut.trim() !== ""
        ? parseInt(manualNoUrut)
        : getNextNoUrutFromTable();

    let data = {
      no_urut: nextNoUrut,
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
    };

    itemTable.row.add(data).draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);

    $("#InspectingFormItem").get(0).reset();
    $("#inspectingitemsform-no_urut").val(getNextNoUrutFromTable());
    $("#inspectingitemsform-ukuran").focus();

    return false;
  });
});

// ========================
//  Tombol Save utama
// ========================
$("#BtnSubmitForm").click(function () {
  $("#InspectingFormHeader").submit();
});
