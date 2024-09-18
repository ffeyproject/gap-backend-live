var itemTable = $('#ItemsTable').DataTable({
    data: keluarItems,
    columns: [
        { "data": function (row, type, set) {return "-";}},
        { "data": "nama_greige" },
        { "data": "grade_name" },
        { "data": "qty_fmt" },
        { "data": "lot_lusi" },
        { "data": "lot_pakan" },
        { "data": "asal_greige_name" },
        {"data": function (row, type, set) {return '<button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';}},
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
    rowCallback: function(row, data, index) {
        $('td', row).eq(0).html(index +1);
    }
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

$('#ItemsTable tbody').on( 'click', 'button.removeItemData', function () {
    var row = itemTable.row( $(this).parents('tr') );
    row.remove().draw(false);
});

$('#GreigeKeluarForm').on('beforeSubmit', function () {
    var $yiiform = $(this);

    let dataItems = [];
    itemTable.rows().every( function (rowIdx, tableLoop, rowLoop) {
        dataItems.push(this.data());
    });

    if(dataItems.length < 1){
        $.alert({
            title: 'Gagal!',
            content: 'Tidak ada item untuk disimpan.!',
        });
    }else {
        $.blockUI();

        let formData = $yiiform.serializeArray();
        formData.push({name: 'items', value: JSON.stringify(dataItems)});
        $.ajax({
            type: $yiiform.attr('method'),
            url: $yiiform.attr('action'),
            data: formData,
            beforeSend: function(jqXHR, settings){},
            success: function(data, textStatus, jqXHR){
                if(data.success) {
                    //console.log(data);
                    window.location.replace(data.redirect);
                } else if (data.validation) {
                    // server validation failed
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                }else {
                    $.alert({
                        title: 'Gagal!',
                        content: 'incorrect server response!',
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                //console.log(jqXHR);
                let msg = textStatus;

                if(jqXHR.responseJSON){
                    msg = jqXHR.responseJSON.message;
                }

                $.alert({
                    title: errorThrown,
                    content: msg,
                });
            },
            complete: function(){
                $.unblockUI();
            }
        });
    }

    return false;
});
