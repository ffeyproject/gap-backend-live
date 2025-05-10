function terimaKartuProses(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  $.confirm({
    title: "Konfirmasi!",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      "<label>Masukan Berat:</label>" +
      '<input type="text" class="berat form-control" value="' +
      beratPeneerimaan +
      '"></input>' +
      "</div>" +
      "</form>",
    buttons: {
      submit: {
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          var ctn = this.$content.find(".berat").val();
          if (!ctn) {
            $.alert("Harap masukan nilai berat!!");
            return false;
          }

          posting(href, ctn);
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

function tolakKartuProses(event) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  $.confirm({
    title: "Konfirmasi!",
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      "<label>Tulis keterangan Penolakan:</label>" +
      '<textarea class="note form-control" rows="6"></textarea>' +
      "</div>" +
      "</form>",
    buttons: {
      submit: {
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          var ctn = this.$content.find(".note").val();
          if (!ctn) {
            $.alert("Harap masukan keterangan penolakan!!");
            return false;
          }

          posting(href, ctn);
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

function posting(href, ctn) {
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
        content: "Kartu Proses berhasil diterima.",
        buttons: {
          ok: function () {
            window.location.replace(indexUrl);
          },
        },
      });
    },
  });
}

function gantiPfp(event, label) {
  event.preventDefault();
  var button = $(event.currentTarget);
  var href = button.attr("href");

  $.confirm({
    title: label,
    //columnClass: 'col-md-6',
    content:
      "" +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      '<label for="InputPfp">Masukan Nomor Pfp:</label>' +
      '<input type="text" class="form-control" id="InputPfp">' +
      '<div class="text-danger input-pfp-hint"></div>' +
      "</div>" +
      "</form>",
    buttons: {
      submit: {
        text: "Submit",
        btnClass: "btn-blue",
        action: function () {
          let ctn = this.$content.find("#InputPfp").val();
          if (!ctn) {
            this.$content
              .find(".input-pfp-hint")
              .html("Harap masukan nomor pfp !!");
            return false;
          } else {
            this.$content.find(".input-pfp-hint").html("");
          }

          $.ajax({
            method: "POST",
            beforeSend: function (jqXHR, settings) {
              $.blockUI({
                message: "<h1>Processing</h1>",
                css: { border: "3px solid #a00" },
              });
            },
            data: { no: ctn },
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
              $.alert({
                title: "Berhasil",
                content: "Ganti Nomor Pfp berhasil",
                buttons: {
                  ok: function () {
                    location.reload();
                    //window.location.replace(indexUrl);
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
