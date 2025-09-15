var itemTable = $('#ItemsTable').DataTable({
    data: setOutItems,
    columns: [
        {
            "data": null,
            "render": function (data, type, row) {
                return '<div style="text-align: center"><button class="btn btn-xs btn-danger removeItemData "><i class="fa fa-trash"></i></button></div>';
            }
        },
        { "data": "id" },
        { "data": "tanggal" },
        { "data": "no_document" },
        { "data": "no_lapak" },
        { "data": "nama_greige" },
        { "data": "grade_name" },
        { "data": "lot_pakan" },
        { "data": "lot_lusi" },
        { "data": "no_mc_weaving" },
        { "data": "qty_fmt" },
        { "data": "asal_greige_name" }
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
});


function addItem(e, data){
    e.preventDefault();

    console.log(data);

    for (i = 0; i < itemTable.rows().data().length; i++) {
        if(data.id === itemTable.rows().data()[i].id){
            $.alert({
                title: 'Tidak Diizinkan',
                content: 'Item ini sudah diinput.',
            });
            return;
        }
    }

    itemTable.row.add(data).draw(false);
}

function removeItem(e, data) {
    e.preventDefault();

    // cari row berdasarkan id
    let rows = itemTable.rows().data();
    for (let i = 0; i < rows.length; i++) {
        if (data.id === rows[i].id) {
            itemTable.row(i).remove().draw(false);
            console.log("Removed:", data);
            return;
        }
    }

    console.log("Item not found to remove:", data);
}




$('#ItemsTable').on('click', '.removeItemData', function () {
    var row = itemTable.row($(this).closest('tr'));
    row.remove().draw(false);
});

function reloadTable(){
    itemTable.clear().draw();
    itemTable.rows.add(setOutItems).draw();
}

$("#BtnSetOut").click(function (e){
    
    
    let keys = [];
    itemTable.rows().every( function (rowIdx, tableLoop, rowLoop) {
        keys.push(this.data().id);
    });
    if(keys.length > 0){
            $.confirm({
                icon: 'fa fa-question',
                title: 'Konfirmasi!',
                content: 'Anda yakin akan mengeluarkan item yang dipilih?!',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    ya: function () {
                        $.ajax({
                            method: 'POST',
                            beforeSend: function (jqXHR, settings) {
                                $.blockUI({
                                    message: '<h1>Processing</h1>',
                                    css: { border: '3px solid #a00' }
                                });
                            },
                            data:{formData:{keys:keys}},
                            url: actionUrl,
                            error: function(jqXHR, textStatus, errorThrown ){
                                var errorObj;
                                try {
                                    errorObj = jQuery.parseJSON(jqXHR.responseText);
                                    if(typeof errorObj !='object'){
                                        errorObj = {name:"Error", message:jqXHR.responseText};
                                    }
                                } catch (e) {
                                    errorObj = {name:"Error", message:jqXHR.responseText};
                                }

                                $.unblockUI();

                                $.alert({
                                    title: errorObj.name,
                                    content: errorObj.message
                                });
                            },
                            success: function(data){
                                $.unblockUI();
                                $.alert({
                                    title: "Berhasil",
                                    content: "Iten berhasil dikeluarkan.",
                                    buttons: {
                                        ok: function () {
                                            location.reload();
                                            //console.log(data);
                                            //window.location.replace(indexUrl);
                                        }
                                    }
                                });
                            }
                        });
                    },
                    batal: function () {},
                }
            });
    }else {
        $.alert({
            icon: 'fa fa-warning',
            title: 'Peringatan..!',
            content: 'Tidak ada data yang dipilih',
            type: 'red',
            typeAnimated: true,
        });
    }
});

// Handler untuk klik tombol satuan
$(document).on('click', '.add-set-out', function(e) {
    e.preventDefault();
    let data = $(this).data('item');   // ambil JSON dari atribut data
    addItem(e, data);
});

// Handler untuk klik tombol "Select semua item"
function selectAll(e) {
    e.preventDefault();
    $('.add-set-out').each(function() {
        //cek apakah data sudah ada di table
        let data = $(this).data('item');
        for (i = 0; i < itemTable.rows().data().length; i++) {
            if(data.id === itemTable.rows().data()[i].id){
                return;
            }
        }
        $(this).trigger('click');
    });
}

