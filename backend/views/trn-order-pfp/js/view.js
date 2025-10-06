function batalOrderPFP(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  $.confirm({
    title: "Konfirmasi!",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      "<label>Tulis keterangan Pembatalan:</label>" +
      '<textarea class="note form-control" rows="6"></textarea>' +
      '<span id="BatalOrderPfpErrorTag" class="text-danger"></span>' +
      "</div>" +
      "</form>",
    buttons: {
      submit: {
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          var ctn = this.$content.find(".note").val();
          if (!ctn) {
            //$.alert('Harap masukan keterangan closing!!');
            $("#BatalOrderPfpErrorTag").html(
              "Harap masukan keterangan pembatalan!!"
            );
            return false;
          }

          $("#BatalOrderPfpErrorTag").html("");

          $.ajax({
            method: "POST",
            beforeSend: function (jqXHR, settings) {
              $.blockUI({
                message: "<h1>Processing</h1>",
                css: { border: "3px solid #a00" },
              });
            },
            data: { data: ctn },
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
            success: function (data) {
              $.unblockUI();
              $.alert({
                title: "Berhasil",
                content: "Order PFP telah dibatalkan.",
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
}

$(document).on("beforeSubmit", "#select-handling-form", function (e) {
  e.preventDefault();
  var form = $(this);
  var modal = $("#trnOrderPfpModal");

  $.ajax({
    url: form.attr("action"),
    type: "post",
    data: form.serialize(),
    success: function (response) {
      if (response.success) {
        modal.modal("hide");
        location.reload();
      } else {
        // kalau gagal, render ulang isi modal (form + error)
        modal.find(".modal-content").html(response);
      }
    },
    error: function () {
      alert("Terjadi kesalahan saat memilih handling.");
    },
  });

  return false;
});
