// ========================
//  Modal info duplikat Fresh Order
//  Dipakai di trn-inspecting/create dan inspecting-mkl-bj/create
//  options: { subtitle, infos: [{label, value}], inspectings: [{id, no, tanggal_inspeksi, status_label}], viewUrl }
// ========================
function escapeHtml(value) {
  return $("<div>").text(value == null || value === "" ? "-" : value).html();
}

function showFreshOrderModal(options) {
  $(".fo-overlay").remove();

  let viewUrl = options.viewUrl;
  let inspectings = options.inspectings || [];

  let infoCards = (options.infos || [])
    .map(function (info) {
      return (
        '<div class="fo-info-card"><span class="fo-info-label">' + escapeHtml(info.label) +
        '</span><span class="fo-info-value">' + escapeHtml(info.value) + "</span></div>"
      );
    })
    .join("");

  let listInspecting = inspectings
    .map(function (it, i) {
      return (
        '<li class="fo-item" style="animation-delay:' + (0.35 + i * 0.07) + 's">' +
        '<span class="fo-item-id">ID #' + escapeHtml(it.id) + "</span>" +
        '<span class="fo-item-main">' +
        '<span class="fo-item-no">' + escapeHtml(it.no) + "</span>" +
        '<span class="fo-item-meta"><i class="fa fa-calendar"></i> ' + escapeHtml(it.tanggal_inspeksi) +
        " &middot; " + escapeHtml(it.status_label) + "</span>" +
        "</span>" +
        '<a class="fo-item-link" target="_blank" href="' + viewUrl +
        (viewUrl.indexOf("?") === -1 ? "?" : "&") + "id=" + encodeURIComponent(it.id) +
        '">Lihat <i class="fa fa-external-link"></i></a>' +
        "</li>"
      );
    })
    .join("");

  let $overlay = $(
    '<div class="fo-overlay" role="dialog" aria-modal="true" aria-labelledby="foTitle">' +
      '<div class="fo-modal">' +
        '<div class="fo-header">' +
          '<button type="button" class="fo-close" aria-label="Tutup">&times;</button>' +
          '<div class="fo-icon"><i class="fa fa-info"></i></div>' +
          '<h4 class="fo-title" id="foTitle">Fresh Order Sudah Ada</h4>' +
          '<p class="fo-subtitle">' + escapeHtml(options.subtitle) + "</p>" +
        "</div>" +
        '<div class="fo-body">' +
          '<div class="fo-info-grid">' + infoCards + "</div>" +
          '<p class="fo-section-title">Inspecting yang sudah ada <span class="fo-badge">' + inspectings.length + "</span></p>" +
          '<ul class="fo-list">' + listInspecting + "</ul>" +
        "</div>" +
        '<div class="fo-footer"><button type="button" class="fo-btn">Mengerti</button></div>' +
      "</div>" +
    "</div>"
  );

  function closeModal() {
    $(document).off("keydown.foModal");
    $overlay.removeClass("fo-show").addClass("fo-hide");
    setTimeout(function () {
      $overlay.remove();
    }, 300);
  }

  $overlay.on("click", ".fo-close, .fo-btn", closeModal);
  $overlay.on("click", function (e) {
    if (e.target === this) closeModal();
  });
  $(document).on("keydown.foModal", function (e) {
    if (e.key === "Escape") closeModal();
  });

  $("body").append($overlay);
  // paksa reflow agar transisi masuk berjalan
  $overlay[0].offsetWidth;
  $overlay.addClass("fo-show");
  $overlay.find(".fo-btn").trigger("focus");
}
