// Form item retur buyer: pilih WO -> surat jalan -> roll asal, atau scan QR / ID roll untuk mengisi otomatis.
// Variabel returForm (multiWo, woId, returId, urlWo, urlSj, urlRoll, urlScan) didefinisikan di child/_form_items.php
// multiWo = true: WO dipilih per baris (input baru), false: WO mengikuti dokumen (ubah).

var returEmptyOptionHtml = '<option value="">-- Tidak diketahui --</option>';
var returBulkRolls = [];

function returAjaxError(jqXHR) {
    var errorObj;
    try {
        errorObj = jQuery.parseJSON(jqXHR.responseText);
        if (typeof errorObj != 'object') {
            errorObj = {name: "Error", message: jqXHR.responseText};
        }
    } catch (e) {
        errorObj = {name: "Error", message: jqXHR.responseText};
    }
    $.alert({title: errorObj.name, content: errorObj.message});
}

function returEscape(text) {
    return $('<div>').text(text).html();
}

// select2 WO diinisialisasi manual (bukan widget kartik) supaya tetap tampil pada baris hasil clone dynamic form
function returInitWoSelect($select) {
    if (!$select.length || typeof $.fn.select2 === 'undefined') {
        return;
    }
    // ikut tema select2 kartik lain di halaman (unit / jenis gudang)
    var themeMatch = ($('.select2-container').first().attr('class') || '').match(/select2-container--(\S+)/);
    $select.select2({
        theme: themeMatch ? themeMatch[1] : 'default',
        width: '100%',
        placeholder: 'Cari WO...',
        allowClear: true,
        minimumInputLength: 3,
        ajax: {
            url: returForm.urlWo,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {q: params.term};
            }
        }
    });
}

$(function () {
    // ditunda agar select2 kartik lain sudah terinisialisasi (untuk membaca temanya)
    setTimeout(function () {
        if (returForm.multiWo) {
            $('.retur-wo, .retur-bulk-wo').each(function () {
                returInitWoSelect($(this));
            });
        }
    }, 0);
});

function returRowWoId($row) {
    return returForm.multiWo ? $row.find('.retur-wo').val() : returForm.woId;
}

function returSjOptionsHtml(results) {
    var html = returEmptyOptionHtml;
    $.each(results, function (i, sj) {
        html += '<option value="' + sj.id + '">' + returEscape(sj.text) + '</option>';
    });
    return html;
}

function returRollOptionsHtml(results) {
    var html = returEmptyOptionHtml;
    $.each(results, function (i, roll) {
        var text = roll.text + (roll.returned ? ' (sudah diretur)' : '');
        html += '<option value="' + roll.id + '" data-qty="' + roll.qty + '" data-grade="' + roll.grade + '"'
            + (roll.returned ? ' disabled' : '') + '>' + returEscape(text) + '</option>';
    });
    return html;
}

function returFetchSj(woId, callback) {
    $.ajax({method: 'GET', url: returForm.urlSj, data: {wo_id: woId}, error: returAjaxError, success: function (data) {
        callback(data.results);
    }});
}

function returFetchRolls(kirimBuyerId, callback) {
    $.ajax({method: 'GET', url: returForm.urlRoll, data: {kirim_buyer_id: kirimBuyerId, retur_id: returForm.returId}, error: returAjaxError, success: function (data) {
        callback(data.results);
    }});
}

function returLoadSj($row, woId, selectedSjId, callback) {
    var $sj = $row.find('.retur-sj');
    $sj.html(returEmptyOptionHtml);
    $row.find('.retur-roll').html(returEmptyOptionHtml);
    if (!woId) {
        return;
    }
    returFetchSj(woId, function (results) {
        $sj.html(returSjOptionsHtml(results));
        if (selectedSjId) {
            $sj.val(selectedSjId);
        }
        if (callback) {
            callback();
        }
    });
}

function returLoadRolls($row, kirimBuyerId, selectedStockId, callback) {
    var $roll = $row.find('.retur-roll');
    $roll.html(returEmptyOptionHtml);
    if (!kirimBuyerId) {
        return;
    }
    returFetchRolls(kirimBuyerId, function (results) {
        $roll.html(returRollOptionsHtml(results));
        if (selectedStockId) {
            $roll.val(selectedStockId).trigger('change');
        }
        if (callback) {
            callback();
        }
    });
}

// set nilai select2 WO tanpa memicu pemuatan ulang surat jalan
function returSetWoSilent($row, woId, woNo) {
    var $wo = $row.find('.retur-wo');
    if (!$wo.find('option[value="' + woId + '"]').length) {
        $wo.append(new Option(woNo, woId, false, false));
    }
    $wo.data('retur-skip-change', true);
    $wo.val(woId).trigger('change');
    $wo.data('retur-skip-change', false);
}

// set WO baris lalu muat surat jalannya, callback dijalankan setelah surat jalan termuat
function returSetWo($row, woId, woNo, callback) {
    if ($row.find('.retur-wo').val() == woId) {
        callback();
        return;
    }
    returSetWoSilent($row, woId, woNo);
    returLoadSj($row, woId, null, callback);
}

// baris terakhir yang masih kosong dipakai ulang, jika tidak ada ditambahkan baris baru
function returTakeEmptyRow() {
    var $last = $('.container-items .item').last();
    if (!$.trim($last.find('.retur-qty').val()) && !$last.find('.retur-roll').val()) {
        return $last;
    }
    $('.add-item').first().trigger('click');
    return $('.container-items .item').last();
}

function returStockIdsInRows() {
    var ids = [];
    $('.container-items .retur-roll').each(function () {
        if ($(this).val()) {
            ids.push(String($(this).val()));
        }
    });
    return ids;
}

$(document).on('change', '.retur-wo', function () {
    if ($(this).data('retur-skip-change')) {
        return;
    }
    returLoadSj($(this).closest('.item'), $(this).val(), null);
});

$(document).on('change', '.retur-sj', function () {
    returLoadRolls($(this).closest('.item'), $(this).val(), null);
});

// roll dipilih -> isi qty & grade sesuai data kirim, masih bisa diubah manual (mis. retur sebagian)
$(document).on('change', '.retur-roll', function () {
    var $opt = $(this).find('option:selected');
    if (!$(this).val()) {
        return;
    }
    var $row = $(this).closest('.item');
    $row.find('.retur-qty').val($opt.data('qty'));
    $row.find('.retur-grade').val($opt.data('grade'));
});

$(document).on('keydown', '.retur-scan', function (e) {
    if (e.key !== 'Enter' && e.keyCode !== 13) {
        return;
    }
    e.preventDefault();

    var $input = $(this);
    var $row = $input.closest('.item');
    var code = $.trim($input.val());
    if (!code) {
        return;
    }

    $.ajax({
        method: 'GET',
        url: returForm.urlScan,
        // tanpa WO -> dicari di semua WO, WO baris terisi otomatis
        data: {wo_id: returRowWoId($row) || '', code: code},
        error: returAjaxError,
        success: function (roll) {
            var $ownRoll = $row.find('.retur-roll');
            var otherIds = returStockIdsInRows();
            if ($ownRoll.val()) {
                otherIds.splice(otherIds.indexOf(String($ownRoll.val())), 1);
            }
            if (otherIds.indexOf(String(roll.stock_id)) !== -1) {
                $.alert({title: 'Info', content: 'Roll #' + roll.stock_id + ' sudah ada di baris lain.'});
                return;
            }

            var fillRoll = function () {
                $row.find('.retur-sj').val(roll.kirim_buyer_id);
                returLoadRolls($row, roll.kirim_buyer_id, roll.stock_id, function () {
                    var $opt = $row.find('.retur-roll option[value="' + roll.stock_id + '"]');
                    if ($opt.prop('disabled')) {
                        $row.find('.retur-roll').val('');
                        $.alert({title: 'Info', content: 'Roll #' + roll.stock_id + ' sudah pernah diretur.'});
                        return;
                    }
                    $input.val('');
                });
            };

            if (returForm.multiWo) {
                returSetWo($row, roll.wo_id, roll.wo_no, fillRoll);
            } else {
                fillRoll();
            }
        }
    });
});

// baris baru otomatis mengikuti WO, surat jalan & grade baris sebelumnya
jQuery(".dynamicform_wrapper").on("afterInsert", function (e, item) {
    var $row = $(item);
    var $prev = $row.prevAll('.item').first();
    $row.find('.retur-scan').val('');
    $row.find('.retur-roll').html(returEmptyOptionHtml);

    if (returForm.multiWo) {
        var $wo = $row.find('.retur-wo');
        $wo.find('option[value!=""]').remove();
        $wo.val('');
        returInitWoSelect($wo);
        $row.find('.retur-sj').html(returEmptyOptionHtml);

        var $prevWo = $prev.find('.retur-wo');
        if ($prevWo.val()) {
            returSetWoSilent($row, $prevWo.val(), $prevWo.find('option:selected').text());
            $row.find('.retur-sj').html($prev.find('.retur-sj').html()).val($prev.find('.retur-sj').val());
        }
    } else {
        // WO sama untuk semua baris, salin daftar surat jalan dari baris sebelumnya
        $row.find('.retur-sj').html($prev.find('.retur-sj').html()).val($prev.find('.retur-sj').val());
    }

    // daftar roll ikut surat jalan yang tersalin
    if ($prev.length && $row.find('.retur-sj').val()) {
        $row.find('.retur-roll').html($prev.find('.retur-roll').html()).val('');
    }
    $row.find('.retur-grade').val($prev.find('.retur-grade').val());
});

/* ---------- Panel tambah banyak roll / qty untuk 1 WO ---------- */

function returBulkWoId() {
    return returForm.multiWo ? $('.retur-bulk-wo').val() : returForm.woId;
}

function returRenderBulkRolls() {
    var $box = $('.retur-bulk-rolls');
    if (!returBulkRolls.length) {
        $box.html($('.retur-bulk-sj').val() ? '<span class="text-muted">Tidak ada roll pada surat jalan ini.</span>' : '');
        return;
    }
    var inRows = returStockIdsInRows();
    var html = '<label style="font-weight: normal"><input type="checkbox" class="retur-bulk-all"> <strong>Pilih semua roll</strong></label>'
        + '<table class="table table-condensed table-bordered" style="margin-bottom: 0"><tbody>';
    $.each(returBulkRolls, function (i, roll) {
        var used = roll.returned || inRows.indexOf(String(roll.id)) !== -1;
        html += '<tr' + (used ? ' class="text-muted"' : '') + '><td style="width: 30px">'
            + '<input type="checkbox" class="retur-bulk-roll" value="' + i + '"' + (used ? ' disabled' : '') + '></td>'
            + '<td>' + returEscape(roll.text) + (roll.returned ? ' (sudah diretur)' : (used ? ' (sudah di item)' : '')) + '</td></tr>';
    });
    html += '</tbody></table>';
    $box.html(html);
}

function returLoadBulkSj(woId) {
    var $sj = $('.retur-bulk-sj');
    $sj.html(returEmptyOptionHtml);
    returBulkRolls = [];
    returRenderBulkRolls();
    if (!woId) {
        return;
    }
    returFetchSj(woId, function (results) {
        $sj.html(returSjOptionsHtml(results));
    });
}

$(document).on('change', '.retur-bulk-wo', function () {
    returLoadBulkSj($(this).val());
});

$(document).on('change', '.retur-bulk-sj', function () {
    returBulkRolls = [];
    returRenderBulkRolls();
    if (!$(this).val()) {
        return;
    }
    returFetchRolls($(this).val(), function (results) {
        returBulkRolls = results;
        returRenderBulkRolls();
    });
});

$(document).on('change', '.retur-bulk-all', function () {
    $('.retur-bulk-roll:not(:disabled)').prop('checked', $(this).prop('checked'));
});

$(document).on('click', '.retur-bulk-add', function () {
    var woId = returBulkWoId();
    if (!woId) {
        $.alert({title: 'Info', content: 'Pilih WO terlebih dahulu.'});
        return;
    }
    var woNo = returForm.multiWo ? $('.retur-bulk-wo option:selected').text() : null;
    var $bulkSj = $('.retur-bulk-sj');
    var sjId = $bulkSj.val();
    var grade = $('.retur-bulk-grade').val();

    var rows = [];
    $('.retur-bulk-roll:checked').each(function () {
        var roll = returBulkRolls[$(this).val()];
        rows.push({stockId: roll.id, qty: roll.qty, grade: roll.grade});
    });

    var invalidQty = [];
    $.each($.trim($('.retur-bulk-qty').val()).split(/[\s,;]+/), function (i, val) {
        if (val === '') {
            return;
        }
        var qty = Number(val.replace(',', '.'));
        if (isNaN(qty) || qty <= 0) {
            invalidQty.push(val);
            return;
        }
        rows.push({stockId: null, qty: qty, grade: grade});
    });

    if (invalidQty.length) {
        $.alert({title: 'Info', content: 'Qty tidak valid: ' + returEscape(invalidQty.join(', '))});
        return;
    }
    if (!rows.length) {
        $.alert({title: 'Info', content: 'Centang roll atau isi qty terlebih dahulu.'});
        return;
    }

    var sjHtml = $bulkSj.html();
    var rollHtml = returRollOptionsHtml(returBulkRolls);
    $.each(rows, function (i, data) {
        var $row = returTakeEmptyRow();
        if (returForm.multiWo) {
            returSetWoSilent($row, woId, woNo);
        }
        $row.find('.retur-sj').html(sjHtml).val(sjId || '');
        $row.find('.retur-roll').html(sjId ? rollHtml : returEmptyOptionHtml).val(data.stockId || '');
        $row.find('.retur-qty').val(data.qty);
        $row.find('.retur-grade').val(data.grade === null || data.grade === undefined ? '' : data.grade);
    });

    $('.retur-bulk-qty').val('');
    returRenderBulkRolls();
});
