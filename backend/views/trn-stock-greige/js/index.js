var itemTable = $("#ItemsTable").DataTable({
  data: mixItems,
  columns: [
    //{ "data": function (row, type, set) {return "-";}},
    { data: "id" },
    { data: "tanggal" },
    { data: "no_document" },
    { data: "no_lapak" },
    { data: "nama_greige" },
    { data: "grade_name" },
    { data: "lot_pakan" },
    { data: "lot_lusi" },
    { data: "no_mc_weaving" },
    { data: "qty_fmt" },
    { data: "asal_greige_name" },
    //{"data": function (row, type, set) {return '<button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';}},
  ],
  ordering: false,
  responsive: true,
  paging: false,
  searching: false,
  info: false,
  /*rowCallback: function(row, data, index) {
        $('td', row).eq(0).html(index +1);
    }*/
});

function addItem(e, data) {
  e.preventDefault();

  console.log(data);

  for (i = 0; i < itemTable.rows().data().length; i++) {
    if (data.id === itemTable.rows().data()[i].id) {
      $.alert({
        title: "Tidak Diizinkan",
        content: "Item ini sudah diinput.",
      });
      return;
    }
  }

  itemTable.row.add(data).draw(false);
}

$("#BtnMixQuality").click(function (e) {
  let keys = [];
  itemTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
    keys.push(this.data().id);
  });

  if (keys.length > 0) {
    if (greigeId && greigeGrade) {
      $.confirm({
        icon: "fa fa-question",
        title: "Konfirmasi!",
        content: "Anda yakin akan mix quality item yang dipilih?!",
        type: "orange",
        typeAnimated: true,
        buttons: {
          ya: function () {
            $.ajax({
              method: "POST",
              beforeSend: function (jqXHR, settings) {
                $.blockUI({
                  message: "<h1>Processing</h1>",
                  css: { border: "3px solid #a00" },
                });
              },
              data: {
                formData: {
                  keys: keys,
                  greigeId: greigeId,
                  greigeGrade: greigeGrade,
                },
              },
              url: actionUrl,
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
                $.alert({
                  title: "Berhasil",
                  content: "Mix Quality Berhasil.",
                  buttons: {
                    ok: function () {
                      location.reload();
                      //console.log(data);
                      //window.location.replace(indexUrl);
                    },
                  },
                });
              },
            });
          },
          batal: function () {},
        },
      });
    } else {
      $.alert({
        icon: "fa fa-warning",
        title: "Peringatan..!",
        content:
          "Greige yang akan dijadikan hasil mix belum dipilih, atau Anda belum menentukan grade nya.",
        type: "red",
        typeAnimated: true,
      });
    }
  } else {
    $.alert({
      icon: "fa fa-warning",
      title: "Peringatan..!",
      content: "Tidak ada data yang dipilih",
      type: "red",
      typeAnimated: true,
    });
  }
});

function changeNotes(event) {
  event.preventDefault();
  let button = $(event.currentTarget);
  let href = button.attr("href");

  let keys = $("#StockGreigeGrid").yiiGridView("getSelectedRows");
  //console.log(keys);

  if (keys.length > 0) {
    //console.log(keys);

    $.confirm({
      columnClass: "medium",
      title: "Change notes..!",
      content:
        "" +
        '<form action="" class="formName">' +
        '<div class="form-group">' +
        "<label>Masukan Keterangan:</label>" +
        '<textarea class="allNotes form-control" rows="6"></textarea>' +
        "</div>" +
        "</form>",
      buttons: {
        submit: {
          text: "Submit",
          btnClass: "btn-blue",
          action: function () {
            var ctn = this.$content.find(".allNotes").val();
            if (!ctn) {
              $.alert("Harap masukan note!!");
              return false;
            }

            $.ajax({
              method: "POST",
              beforeSend: function (jqXHR, settings) {
                $.blockUI({
                  message: "<h1>Processing</h1>",
                  css: { border: "3px solid #a00" },
                });
              },
              data: { note: ctn, ids: keys },
              url: href,
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
              success: function (response) {
                $.unblockUI();

                //console.log(response);

                $.alert({
                  title: "Success",
                  content: "Change notes success",
                  buttons: {
                    ok: function () {
                      location.reload();
                    },
                  },
                });
              },
            });
          },
        },
        batal: function () {},
      },
      onContentReady: function () {
        // bind to events
        var jc = this;
        this.$content.find("form").on("submit", function (e) {
          // if the user submits the form by pressing enter in the field.
          e.preventDefault();
          jc.formSubmit.trigger("click"); // reference the button and click it
        });
      },
    });
  } else {
    $.alert({
      title: "Warning!",
      content: "No items selected.",
    });
  }
}

function changeKetWeaving(event) {
  event.preventDefault();
  let button = $(event.currentTarget);
  let href = button.attr("href");

  let keys = $("#StockGreigeGrid").yiiGridView("getSelectedRows");
  //console.log(keys);

  if (keys.length > 0) {
    //console.log(keys);

    $.confirm({
      columnClass: "medium",
      title: "Change Ket. Weaving ..!",
      content:
        "" +
        '<form action="" class="formName">' +
        '<div class="form-group">' +
        "<label>Masukan Keterangan Weaving:</label>" +
        '<select class="ketWeaving form-control">' +
        '<option value="1">Salur Muda</option>' +
        '<option value="2">Salur Tua</option>' +
        '<option value="3">Salur Abnormal</option>' +
        '<option value="4">Normal</option>' +
        '<option value="5">Lain-lain</option>' +
        '<option value="6">TSD</option>' +
        "</select>" +
        "</div>" +
        "</form>",
      buttons: {
        submit: {
          text: "Submit",
          btnClass: "btn-blue",
          action: function () {
            var ctn = this.$content.find(".ketWeaving").val();
            if (!ctn) {
              $.alert("Harap masukan ket. weaving !!");
              return false;
            }

            $.ajax({
              method: "POST",
              beforeSend: function (jqXHR, settings) {
                $.blockUI({
                  message: "<h1>Processing</h1>",
                  css: { border: "3px solid #a00" },
                });
              },
              data: { ket_weaving: ctn, ids: keys },
              url: href,
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
              success: function (response) {
                $.unblockUI();

                //console.log(response);

                $.alert({
                  title: "Success",
                  content: "Change Ket. Weaving success",
                  buttons: {
                    ok: function () {
                      location.reload();
                    },
                  },
                });
              },
            });
          },
        },
        batal: function () {},
      },
      onContentReady: function () {
        // bind to events
        var jc = this;
        this.$content.find("form").on("submit", function (e) {
          // if the user submits the form by pressing enter in the field.
          e.preventDefault();
          jc.formSubmit.trigger("click"); // reference the button and click it
        });
      },
    });
  } else {
    $.alert({
      title: "Warning!",
      content: "No items selected.",
    });
  }
}

function duplicateStock(event) {
  event.preventDefault();
  let button = $(event.currentTarget);
  let href = button.attr("href");

  // Ambil checkbox yang terpilih
  let keys = $("#StockGreigeGrid").yiiGridView("getSelectedRows");

  if (keys.length > 0) {
    $.confirm({
      columnClass: "medium",
      title: "Duplikat Stock!",
      content:
        "Apakah Anda yakin ingin menduplikat stock yang dipilih ke Stock Opname?",
      buttons: {
        ya: {
          btnClass: "btn-blue",
          action: function () {
            $.ajax({
              method: "POST",
              beforeSend: function () {
                $.blockUI({
                  message: "<h1>Processing...</h1>",
                  css: { border: "3px solid #a00" },
                });
              },
              data: { ids: keys },
              url: href,
              error: function (jqXHR) {
                $.unblockUI();
                let errorObj;
                try {
                  errorObj = jQuery.parseJSON(jqXHR.responseText);
                } catch (e) {
                  errorObj = { name: "Error", message: jqXHR.responseText };
                }
                $.alert({ title: errorObj.name, content: errorObj.message });
              },
              success: function (response) {
                $.unblockUI();
                $.alert({
                  title: "Berhasil",
                  content: "Stock berhasil diduplikat ke Stock Opname.",
                  buttons: {
                    ok: function () {
                      location.reload();
                    },
                  },
                });
              },
            });
          },
        },
        batal: function () {},
      },
    });
  } else {
    $.alert({ title: "Peringatan!", content: "Tidak ada item yang dipilih." });
  }
}
