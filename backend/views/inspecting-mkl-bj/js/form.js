const formatterNumber = new Intl.NumberFormat("ID-id", {
  style: "decimal",
  minimumFractionDigits: 0,
  maximumFractionDigits: 8,
});

// === Fungsi ambil nomor urut berikutnya ===
function getNextNoUrutFromTable() {
  let dataItems = itemTable.rows().data().toArray();
  if (dataItems.length === 0) return 1;
  let maxUrut = Math.max(...dataItems.map((d) => parseInt(d.no_urut || 0)));
  return maxUrut + 1;
}

// === Inisialisasi DataTable ===
var itemTable = $("#InspectingItemTable").DataTable({
  data: inspectingItems,
  columns: [
    { data: null, title: "No", defaultContent: "" }, // Nomor otomatis
    { data: "no_urut", title: "No Urut", defaultContent: "-" },
    { data: "gradeLabel", title: "Grade" },
    { data: "qty", title: "Ukuran" },
    { data: "join_piece", title: "Join Piece" },
    { data: "lot_no", title: "No Lot" },
    { data: "defect", title: "Defect" },
    { data: "note", title: "Keterangan" },
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
      title: "Action",
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
    // Kolom pertama = nomor urut tabel otomatis
    $("td", row)
      .eq(0)
      .html(index + 1);
    // Kolom kedua = No Urut (manual)
    $("td", row)
      .eq(1)
      .html(data.no_urut ?? "-");
  },
});

// === Hapus item ===
$("#InspectingItemTable tbody").on(
  "click",
  "button.removeItemData",
  function () {
    // 1. Hapus dulu row
    let row = itemTable.row($(this).parents("tr"));
    row.remove();

    // 2. Reset nomor urut seluruh item di DataTable
    itemTable.rows().every(function (index) {
      let d = this.data();
      d.no_urut = index + 1;
      this.data(d);
    });

    // 3. Redraw tabel
    itemTable.draw(false);

    // 4. Update counter
    $("#ItemCounter").html(itemTable.rows().data().length);

    // 5. Update input form no_urut (INI YANG PENTING!!)
    $("#inspectingmklbjitems-no_urut").val(getNextNoUrutFromTable());
  }
);

// === Edit item ===
$("#InspectingItemTable tbody").on("click", "button.editItemData", function () {
  let row = itemTable.row($(this).parents("tr"));
  let data = row.data();
  let rowIndex = row.index();

  $.confirm({
    title: "Edit Item",
    content: `
            <form class="formName">
                <div class="form-group"><label>No Urut</label><input type="number" class="editNoUrut form-control" value="${
                  data.no_urut ?? ""
                }"/></div>
                <div class="form-group"><label>Grade</label>
                    <select id="optionGrade" class="editGrade form-control">
                        <option value="1">Grade A</option>
                        <option value="2">Grade B</option>
                        <option value="3">Grade C</option>
                        <option value="4">Piece Kecil</option>
                        <option value="5">Sample</option>
                        <option value="7">Grade A+</option>
                        <option value="8">Grade A*</option>
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
          let grade = this.$content.find("#optionGrade").val();
          let gradeLabel = this.$content
            .find("#optionGrade option:selected")
            .text();
          let qty = this.$content.find(".editUkuran").val().trim();

          if (!grade) return $.alert("Grade harus diisi.");
          if (!qty) return $.alert("Ukuran harus diisi.");

          let join_piece = this.$content.find(".editJoinPiece").val().trim();
          let lot_no = this.$content.find(".editLotNo").val().trim();
          let defect = this.$content.find(".editDefect").val().trim();
          let note = this.$content.find(".editKeterangan").val().trim();

          itemTable
            .row(rowIndex)
            .data({
              id: data.id ?? null,
              no_urut: no_urut || data.no_urut,
              grade,
              gradeLabel,
              qty,
              join_piece,
              lot_no,
              defect,
              note,
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

// === Simpan semua ke server ===
$("#InspectingFormHeader").on("afterInit", function () {
  $("#InspectingFormHeader").on("beforeSubmit", function () {
    let $yiiform = $(this);
    let dataItems = itemTable.rows().data().toArray();

    if (dataItems.length < 1) {
      $.alert({ title: "Gagal!", content: "Tidak ada item untuk disimpan." });
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

// === Tambah item ===
$("#InspectingFormItem").on("afterInit", function () {
  $("#InspectingFormItem").on("beforeSubmit", function () {
    let manualNoUrut = $("#inspectingmklbjitems-no_urut").val().trim();
    let nextNoUrut =
      manualNoUrut !== "" ? parseInt(manualNoUrut) : getNextNoUrutFromTable();

    let data = {
      id: null,
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
    };

    itemTable.row.add(data).draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);

    // Reset form item dan isi otomatis no urut berikutnya
    $("#InspectingFormItem").get(0).reset();
    $("#inspectingmklbjitems-no_urut").val(getNextNoUrutFromTable());
    $("#inspectingmklbjitems-qty").focus();

    return false;
  });
});

// === Tombol Simpan ===
$("#BtnSubmitForm").click(function () {
  $("#InspectingFormHeader").submit();
});
