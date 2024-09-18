var itemTable = $('#ItemsTable').DataTable({
    data: mutasiItems,
    columns: [
        { "data": function (row, type, set) {return "-";}},
        { "data": "no_wo" },
        { "data": "color" },
        { "data": "no_lot" },
        { "data": "motif" },
        { "data": "qty_fmt" },
        { "data": "unit" },
        {"data": function (row, type, set) {return '<button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';}},
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
    rowCallback: function(row, data, index) {
        //console.log(data);

        $('td', row).eq(0).html(index +1);
        /*itemTotalQty += data.qty;
        $("#TotalQty").html(itemTotalQty);*/
    },
    /*"footerCallback": function ( row, data, start, end, display ) {
        //https://phppot.com/jquery/calculate-sum-total-of-datatables-column-using-footer-callback/

        var api = this.api(), data;

        // converting to interger to find total
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        $( api.column( 2 ).footer() ).html("sdfsfsfsdfd");

        console.log(intVal);
    }*/
});

function addItem(e, data){
    e.preventDefault();
    //console.log(data);

    for (i = 0; i < itemTable.rows().data().length; i++) {
        let stData = itemTable.rows().data()[i];
        if(data.stock_id === stData.stock_id){
            $.alert({
                title: 'Tidak Diizinkan',
                content: 'Item ini sudah diinput.',
            });
            return;
        }
    }

    itemTable.row.add(data).draw(false);

    itemTotalQty += data.qty;
    $("#TotalQty").html(itemTotalQty);
}

$('#ItemsTable tbody').on( 'click', 'button.removeItemData', function () {
    var row = itemTable.row( $(this).parents('tr') );
    //console.log(row.data());

    itemTotalQty -= row.data().qty;

    row.remove().draw(false);

    $("#TotalQty").html(itemTotalQty);
});

$('#GdJadiMutasiForm').on('beforeSubmit', function () {
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
