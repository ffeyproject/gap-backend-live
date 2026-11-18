var itemTable = $('#ItemsTable').DataTable({
    data: [],
    columns: [
        {
            "data": null,
            "render": function (data, type, row, meta) {
                return meta.row + 1;
            },
            "className": "text-center"
        },
        { "data": "id", "className": "text-center" },
        { "data": "gudang_jadi_id", "className": "text-center" },
        { "data": "no_wo" },
        { "data": "motif" },
        { "data": "grade_name", "className": "text-center" },
        { "data": "qty_fmt", "className": "text-right" },
        { "data": "unit_name", "className": "text-center" },
        { "data": "nama_buyer" },
        {
            "data": null,
            "render": function (data, type, row) {
                return '<button type="button" class="btn btn-xs btn-danger btn-remove-item" data-id="' + row.id + '" title="Hapus dari daftar jual"><i class="glyphicon glyphicon-trash"></i></button>';
            },
            "className": "text-center"
        }
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
    language: {
        emptyTable: "Belum ada item yang diceklis / dipilih untuk dijual. Silakan ceklis item pada tabel di atas."
    }
});

function addItem(e, data, silent) {
    if (e && e.preventDefault) e.preventDefault();

    var currentData = itemTable.rows().data();
    for (var i = 0; i < currentData.length; i++) {
        if (currentData[i].id == data.id) {
            if (!silent) {
                $.alert({
                    title: 'Pemberitahuan',
                    content: 'Item ID ' + data.id + ' sudah ada di daftar jual.',
                });
            }
            return false;
        }
    }

    itemTable.row.add(data).draw(false);

    // Tandai checkbox jika terlihat di halaman sekarang
    var $cb = $('.item-checkbox[value="' + data.id + '"]');
    if ($cb.length > 0) {
        $cb.prop('checked', true);
        $cb.closest('tr').addClass('info');
    }

    updateTotals();
    updateGridCheckCounter();
    updateSelectAllState();
    return true;
}

function removeItem(id) {
    var currentData = itemTable.rows().data();
    var targetIndex = -1;
    for (var i = 0; i < currentData.length; i++) {
        if (currentData[i].id == id) {
            targetIndex = i;
            break;
        }
    }

    if (targetIndex !== -1) {
        itemTable.row(targetIndex).remove().draw(false);

        // Hapus centang di grid jika terlihat
        var $cb = $('.item-checkbox[value="' + id + '"]');
        if ($cb.length > 0) {
            $cb.prop('checked', false);
            $cb.closest('tr').removeClass('info');
        }

        updateTotals();
        updateGridCheckCounter();
        updateSelectAllState();
    }
}

function updateTotals() {
    var data = itemTable.rows().data();
    var totalItems = data.length;
    var unitTotals = {};
    var totalQty = 0;

    for (var i = 0; i < data.length; i++) {
        var u = data[i].unit_name || '-';
        var q = parseFloat(data[i].qty) || 0;
        totalQty += q;

        if (!unitTotals[u]) {
            unitTotals[u] = 0;
        }
        unitTotals[u] += q;
    }

    var unitSummaries = [];
    for (var unit in unitTotals) {
        unitSummaries.push(unitTotals[unit].toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + unit);
    }
    var unitStr = unitSummaries.length > 0 ? unitSummaries.join(' + ') : '-';

    $('#selected-summary').text(totalItems + ' Item Terpilih');
    $('#total-qty-cell').text(totalQty.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#total-unit-cell').text(Object.keys(unitTotals).join(', ') || '-');
    $('#total-summary-text').text(totalItems + ' Roll (' + unitStr + ')');
}

function updateGridCheckCounter() {
    var checkedCount = $('.item-checkbox:checked').length;
    $('#grid-checked-counter').text(checkedCount + ' item diceklis di halaman ini');
}

function updateSelectAllState() {
    var total = $('.item-checkbox:not(:disabled)').length;
    var checked = $('.item-checkbox:not(:disabled):checked').length;
    $('.select-on-check-all').prop('checked', total > 0 && total === checked);
}

function syncCheckboxes() {
    var selectedMap = {};
    var data = itemTable.rows().data();
    for (var i = 0; i < data.length; i++) {
        selectedMap[data[i].id] = true;
    }

    $('.item-checkbox').each(function () {
        var id = $(this).val();
        var isSelected = !!selectedMap[id];
        $(this).prop('checked', isSelected);
        $(this).closest('tr').toggleClass('info', isSelected);
    });

    updateSelectAllState();
    updateGridCheckCounter();
}

// Checkbox satu per satu
$(document).on('change', '.item-checkbox', function () {
    var isChecked = $(this).is(':checked');
    var dataStr = $(this).attr('data-item');
    if (!dataStr) return;

    try {
        var data = JSON.parse(dataStr);
        if (isChecked) {
            $(this).closest('tr').addClass('info');
            addItem(null, data, true);
        } else {
            $(this).closest('tr').removeClass('info');
            removeItem(data.id);
        }
    } catch (e) {
        console.error(e);
    }

    updateGridCheckCounter();
    updateSelectAllState();
});

// Select All checkbox di header
$(document).on('change', '.select-on-check-all', function () {
    var isChecked = $(this).is(':checked');
    $('.item-checkbox:not(:disabled)').each(function () {
        var $cb = $(this);
        var dataStr = $cb.attr('data-item');
        if (!dataStr) return;

        try {
            var data = JSON.parse(dataStr);
            if (isChecked) {
                $cb.prop('checked', true);
                $cb.closest('tr').addClass('info');
                addItem(null, data, true);
            } else {
                $cb.prop('checked', false);
                $cb.closest('tr').removeClass('info');
                removeItem(data.id);
            }
        } catch (e) {
            console.error(e);
        }
    });

    updateGridCheckCounter();
});

// Tombol "Tambah Semua yang Diceklis"
$(document).on('click', '#btnAddChecked', function () {
    var added = 0;
    $('.item-checkbox:checked').each(function () {
        var dataStr = $(this).attr('data-item');
        if (dataStr) {
            try {
                var data = JSON.parse(dataStr);
                if (addItem(null, data, true)) {
                    added++;
                }
            } catch (e) {}
        }
    });

    var totalChecked = $('.item-checkbox:checked').length;
    if (totalChecked === 0) {
        $.alert({
            title: 'Perhatian',
            content: 'Belum ada item yang diceklis pada tabel di atas. Silakan beri tanda centang pada item yang ingin dipilih.',
            type: 'orange'
        });
    } else if (added > 0) {
        $.alert({
            title: 'Berhasil',
            content: added + ' item baru berhasil ditambahkan ke daftar jual.',
            type: 'green'
        });
    } else {
        $.alert({
            title: 'Informasi',
            content: 'Semua item yang diceklis (' + totalChecked + ' item) sudah ada di dalam daftar jual.',
            type: 'blue'
        });
    }
});

// Tombol "Hapus Ceklis Halaman Ini"
$(document).on('click', '#btnUncheckPage', function () {
    $('.item-checkbox:checked').each(function () {
        var id = $(this).val();
        $(this).prop('checked', false);
        $(this).closest('tr').removeClass('info');
        removeItem(id);
    });
    $('.select-on-check-all').prop('checked', false);
    updateGridCheckCounter();
});

// Tombol Hapus per baris di tabel jual
$(document).on('click', '.btn-remove-item', function () {
    var id = $(this).data('id');
    removeItem(id);
});

// Tombol Kosongkan Semua Daftar
$(document).on('click', '#btnClearItems', function () {
    if (itemTable.rows().data().length === 0) {
        $.alert('Daftar item untuk dijual sudah kosong.');
        return;
    }

    $.confirm({
        title: 'Konfirmasi',
        content: 'Yakin ingin mengosongkan semua item yang sudah dipilih untuk dijual?',
        buttons: {
            ya: {
                text: 'Ya, Kosongkan',
                btnClass: 'btn-danger',
                action: function () {
                    itemTable.clear().draw();
                    $('.item-checkbox').prop('checked', false).closest('tr').removeClass('info');
                    $('.select-on-check-all').prop('checked', false);
                    updateTotals();
                    updateGridCheckCounter();
                }
            },
            batal: function () {}
        }
    });
});

// PJAX Success/End listener: pastikan checkbox yang itemnya sudah di tabel tetap tercentang
$(document).on('pjax:end', function () {
    syncCheckboxes();
});

$("#BtnJual").click(function(){
    let data = itemTable.rows().data();
    let dataItems = [];

    let greiges = [];
    let woBuyers = [];
    let qtys = [];

    for (i = 0; i < data.length; i++) {
        if(!greiges.includes(data[i].motif)){
            greiges.push(data[i].motif);
            qtys[data[i].motif] = {qty: data[i].qty, unit: data[i].unit_name};
        }else{
            qtys[data[i].motif]['qty'] += data[i].qty;
        }

        if(!woBuyers.includes(data[i].no_wo + " / " + data[i].nama_buyer)){
            woBuyers.push(data[i].no_wo + " / " + data[i].nama_buyer);
        }

        dataItems.push(data[i]);
    }

    let greigesStr = greiges.join(" + ");
    let qtyArr = [];

    for(let qty in qtys){
        qtyArr.push(qtys[qty].qty + " " + qtys[qty].unit);
    }

    let qtyArrStr = qtyArr.join(" + ");

    if(data.length){

        $.confirm({
            columnClass: 'x-large',
            title: 'Konfirmasi!',
            content: formJual,
            buttons: {
                submit: {
                    text: 'Submit',
                    btnClass : 'btn-blue',
                    action: function(){
                        let buyerId = this.$content.find("#NamaBuyer option:selected").val();
                        if(!buyerId){
                            $.alert('Harap masukan nama buyer !!');
                            return false;
                        }

                        let isResmi = this.$content.find("input[name='is_resmi']:checked").val();
                        if(!isResmi){
                            $.alert('Harap masukan keterangan resmi atau tidak resmi !!');
                            return false;
                        }

                        let grade = this.$content.find("input[name='grade']:checked").val();
                        if(!grade){
                            $.alert('Harap masukan keterangan grade !!');
                            return false;
                        }

                        let harga = this.$content.find("#harga").val();
                        if(!harga){
                            $.alert('Harap masukan keterangan harga !!');
                            return false;
                        }

                        let no_po = this.$content.find("#no_po").val();
                        if(!no_po){
                            $.alert('Harap masukan keterangan nomor PO !!');
                            return false;
                        }

                        let ongkir = this.$content.find("input[name='ongkir']:checked").val();
                        if(!ongkir){
                            $.alert('Harap masukan keterangan ongkos kirim !!');
                            return false;
                        }

                        let pembayaran = this.$content.find("#pembayaran").val();
                        if(!pembayaran){
                            $.alert('Harap masukan keterangan pembayaran !!');
                            return false;
                        }

                        let tglKirim = this.$content.find("#tglKirim").val();
                        if(!tglKirim){
                            $.alert('Harap masukan keterangan tanggal pengiriman !!');
                            return false;
                        }

                        let komisi = this.$content.find("#komisi").val();
                        if(!komisi){
                            $.alert('Harap masukan keterangan komisi !!');
                            return false;
                        }

                        let jenisOrder = this.$content.find("input[name='jenisOrder']:checked").val();
                        if(!jenisOrder){
                            $.alert('Harap masukan keterangan jenis order !!');
                            return false;
                        }

                        let keterangan = this.$content.find("#keterangan").val();
                        if(!keterangan){
                            $.alert('Harap masukan keterangan !!');
                            return false;
                        }

                        let dataHeader = {
                            customer_id: buyerId,
                            is_resmi: isResmi,
                            grade: grade,
                            harga: harga,
                            no_po: no_po,
                            ongkir: ongkir,
                            pembayaran: pembayaran,
                            tanggal_pengiriman: tglKirim,
                            komisi: komisi,
                            jenis_order: jenisOrder,
                            keterangan: keterangan
                        };

                        let dataPost = {header: dataHeader, items: dataItems};

                        $.ajax({
                            method: 'POST',
                            beforeSend: function (jqXHR, settings) {
                                $.blockUI({
                                    message: '<h1>Processing</h1>',
                                    css: { border: '3px solid #a00' }
                                });
                            },
                            data:dataPost,
                            url: actionUrl,
                            error: function(jqXHR, textStatus, errorThrown ){
                                $.unblockUI();

                                $.alert({
                                    title: textStatus,
                                    content: jqXHR.responseText,
                                });
                            },
                            success: function(data){
                                $.unblockUI();
                                $.alert({
                                    title: "Berhasil",
                                    content: "Data berhasil disimpan.",
                                    buttons: {
                                        ok: function () {
                                            window.location.reload();
                                        }
                                    }
                                });
                            }
                        });
                    }
                },
                batal: function () {}
            },
            onContentReady: function(){
                // bind to events
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    // if the user submits the form by pressing enter in the field.
                    e.preventDefault();
                    jc.formSubmit.trigger('click'); // reference the button and click it
                });

                this.$content.find("#tglKirim").datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                });

                this.$content.find("#NamaBuyer").select2({
                    dropdownParent: $('#FormMutasi'),
                    placeholder: 'Masukan nama buyer..',
                    minimumInputLength: 3,
                    ajax: {
                        url: urlCustomerSearch,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                });

                $("#MotifItem").val(greigesStr);
                $("#MotifQty").val(qtyArrStr);

                let ulEl = document.getElementById("ulEl");
                for (i = 0; i < woBuyers.length; i++) {
                    let li = document.createElement("li");
                    li.appendChild(document.createTextNode(woBuyers[i]));
                    ulEl.appendChild(li);
                }
            }
        });
    }else {
        $.alert({
            title: 'Gagal',
            content: 'Tidak ada data item yang dipilih.',
        });
    }
});
