var itemTable = $("#ItemsTable").DataTable({
  data: [],
  columns: [
    {
      data: function (row, type, set, meta) {
        return meta.row + 1;
      },
    },
    { data: "id" },
    { data: "jenisGudangName" },
    { data: "marketingName" },
    { data: "customerName" },
    { data: "scNo" },
    { data: "woNo" },
    { data: "color" },
    { data: "source" },
    { data: "source_ref" },
    { data: "unitName" },
    { data: "qtyFormatted" },
    { data: "gradeName" },
    { data: "motif" },
    { data: "date" },
    {
      data: function (row, type, set) {
        return '<button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';
      },
    },
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

$("#ItemsTable tbody").on("click", "button.removeItemData", function () {
  itemTable.row($(this).parents("tr")).remove().draw();
  updateRowNumbers();
});

function addSelectedItem(e, data) {
  e.preventDefault();

  let rowsData = itemTable.rows().data();
  for (i = 0; i < rowsData.length; i++) {
    if (data.id === rowsData[i].id) {
      $.alert({
        title: "Tidak Diizinkan",
        content: "Item ini sudah diinput.",
      });
      return;
    }
  }

  itemTable.row.add(data).draw(false);
  updateRowNumbers();
}

function readyForSend(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  let ids = [];
  let data = itemTable.rows().data(),
    i;
  for (i = 0; i < data.length; i++) {
    ids.push(data[i].id);
  }

  if (ids.length > 0) {
    //console.log(ids);
    $.ajax({
      method: "POST",
      beforeSend: function (jqXHR, settings) {
        $.blockUI({
          message: "<h1>Processing</h1>",
          css: { border: "3px solid #a00" },
        });
      },
      data: { formData: ids },
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
        console.log(response);
        $.confirm({
          title: "Berhasil!",
          content: "Seting siap kirim berhasil",
          buttons: {
            ok: function () {
              location.reload();
            },
          },
        });
      },
    });
  } else {
    $.alert({
      title: "Peringatan!",
      content: "Tidak ada data yang dipilih",
    });
  }
}

function setAsStock(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  let ids = [];
  let data = itemTable.rows().data(),
    i;
  for (i = 0; i < data.length; i++) {
    ids.push(data[i].id);
  }

  if (ids.length > 0) {
    $.ajax({
      method: "POST",
      beforeSend: function (jqXHR, settings) {
        $.blockUI({
          message: "<h1>Processing</h1>",
          css: { border: "3px solid #a00" },
        });
      },
      data: { formData: ids },
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
        console.log(response);
        $.confirm({
          title: "Berhasil!",
          content: "Seting stock berhasil",
          buttons: {
            ok: function () {
              location.reload();
            },
          },
        });
      },
    });
  } else {
    $.alert({
      title: "Peringatan!",
      content: "Tidak ada data yang dipilih",
    });
  }
}

function mutasikanKeExFinish(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  let ids = [];
  let datas = itemTable.rows().data(),
    i;
  for (i = 0; i < datas.length; i++) {
    ids.push(datas[i].id);
  }

  if (ids.length > 0) {
    $.confirm({
      columnClass: "large",
      title: "Konfirmasi!",
      content:
        "" +
        '<form action="" class="formName">' +
        '<div class="row"><div class="col-sm-6"><div class="form-group"><label for="noRef">No Referensi</label><input type="text" class="form-control" id="noRef"></div></div><div class="col-sm-6"><div class="form-group"><label for="pemohon">Pemohon</label><input type="text" class="form-control" id="pemohon"></div></div></div>' +
        '<div class="form-group"><label>Note:</label><textarea class="note form-control" rows="3"></textarea></div>' +
        "</form>",
      buttons: {
        submit: {
          text: "Submit",
          btnClass: "btn-blue",
          action: function () {
            var ref = this.$content.find("#noRef").val();
            if (!ref) {
              $.alert("Harap masukan nomor referensi !!");
              return false;
            }

            var pemohon = this.$content.find("#pemohon").val();
            if (!pemohon) {
              $.alert("Harap masukan nama pemohon !!");
              return false;
            }

            var note = this.$content.find(".note").val();
            if (!note) {
              $.alert("Harap masukan keterangan !!");
              return false;
            }

            //postingRejectMo(href, ctn);
            $.ajax({
              method: "POST",
              beforeSend: function (jqXHR, settings) {
                $.blockUI({
                  message: "<h1>Processing</h1>",
                  css: { border: "3px solid #a00" },
                });
              },
              data: {
                data: { ref: ref, pemohon: pemohon, note: note, ids: ids },
              },
              url: href,
              error: function (jqXHR, textStatus, errorThrown) {
                $.unblockUI();

                $.alert({
                  title: "Error",
                  content: textStatus,
                });
              },
              success: function (data) {
                $.unblockUI();
                $.alert({
                  title: "Berhasil",
                  content: "Mutasi Ex Finish Berhasil.",
                  buttons: {
                    ok: function () {
                      window.location.reload();
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
      title: "Peringatan!",
      content: "Tidak ada data yang dipilih",
    });
  }
}

function pindahGudang(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  let ids = [];
  let datas = itemTable.rows().data(),
    i;
  for (i = 0; i < datas.length; i++) {
    ids.push(datas[i].id);
  }

  let select2Data = $("#JenisGudangSelect").select2("data")[0];

  if (ids.length > 0 && select2Data.id !== "") {
    $.confirm({
      title: "Konfirmasi!",
      content:
        "Anda yakin akan memindahkan jenis gudang item terpilih ke gudang " +
        select2Data.text +
        "?",
      buttons: {
        ok: function () {
          $.ajax({
            method: "POST",
            beforeSend: function (jqXHR, settings) {
              $.blockUI({
                message: "<h1>Processing</h1>",
                css: { border: "3px solid #a00" },
              });
            },
            data: { ids: ids, jenis_gudang: select2Data.id },
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

              $.confirm({
                title: "Berhasil!",
                content:
                  "Stock berhasil dipindahkan ke gudang " +
                  select2Data.text +
                  ".",
                buttons: {
                  ok: function () {
                    location.reload();
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
      title: "Peringatan!",
      content: "Tidak ada data yang dipilih atau jenis gudang belum dipilih.",
    });
  }
}

function printLabel(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  let select2Data = $("#JenisLocationSelect").select2("data")[0];

  let url = window.location.origin + href + "&locs_code=" + select2Data.text;

  window.location.href = url;
}

function openQRWindow(event, id) {
  var checkbox1 = document.querySelector(".checkbox-param1#param1-" + id);
  var param1Value = checkbox1 ? (checkbox1.checked ? 1 : 0) : 0;
  var checkbox2 = document.querySelector(".checkbox-param2#param2-" + id);
  var param2Value = checkbox2 ? (checkbox2.checked ? 1 : 0) : 0;
  var checkbox3 = document.querySelector(".checkbox-param3#param3-" + id);
  var param3Value = checkbox3 ? (checkbox3.checked ? 1 : 0) : 0;

  // Use id and param2Value as needed
  // var url = baseUrl + 'trn-gudang-jadi/qr&id=' + id + '&param1=' + param1Value + '&param2=' + param2Value + '&param3=' + param3Value;
  var url =
    baseUrl +
    "trn-gudang-jadi/qr?id=" +
    id +
    "&param1=" +
    param1Value +
    "&param2=" +
    param2Value +
    "&param3=" +
    param3Value;

  // Open the URL in a new window
  window.open(url);
}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/*
$(document).on('pjax:success', function() {
    //setInfoSelected();
});*/

function choseAllItems(params) {
  $(".add-mix-btn").each(function () {
    $(this).click(); // Trigger click untuk setiap tombol dengan class 'add-mix-btn'
  });
}

function updateRowNumbers() {
  $("#ItemsTable tbody tr").each(function (index) {
    $(this)
      .find("td:first")
      .text(index + 1); // Memperbarui nomor baris
  });
}
