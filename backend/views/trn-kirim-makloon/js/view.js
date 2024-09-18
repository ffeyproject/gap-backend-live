//
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

    // If none of the checkboxes are checked
    if($chkbox_checked.length === 0){
        chkbox_select_all.checked = false;
        if('indeterminate' in chkbox_select_all){
            chkbox_select_all.indeterminate = false;
        }

        // If all of the checkboxes are checked
    } else if ($chkbox_checked.length === $chkbox_all.length){
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all){
            chkbox_select_all.indeterminate = false;
        }

        // If some of the checkboxes are checked
    } else {
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all){
            chkbox_select_all.indeterminate = true;
        }
    }
}

//Stock----------------------------------------------------------------------------------------------------------------------
// Array holding selected row IDs
var rows_selected = [];
var table = $('#TableStock').DataTable({
    searching: false,
    paging: false,
    ordering: false,
    sort: false,
    data: dataStocks,
    columns: [
        { data: 'checkbox' },
        { data: 'jenis_gudang_name' },
        { data: 'source_name' },
        { data: 'qty_fmt' },
        { data: 'unit_name' },
        { data: 'note' }
    ],
    'columnDefs': [{
        'targets': 0,
        'width': '1%',
        'className': 'dt-body-center',
        'render': function (data, type, full, meta){
            return '<input type="checkbox">';
        }
    }],
    'order': [[1, 'asc']],
    'rowCallback': function(row, data, dataIndex){
        // Get row ID
        var rowId = data.id;
        //console.log(rowId);

        // If row ID is in the list of selected row IDs
        if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
        }
    }
});

// Handle click on checkbox
$('#TableStock tbody').on('click', 'input[type="checkbox"]', function(e){
    var $row = $(this).closest('tr');

    // Get row data
    var data = table.row($row).data();

    // Get row ID
    var rowId = data.id;

    // Determine whether row ID is in the list of selected row IDs
    var index = $.inArray(rowId, rows_selected);

    // If checkbox is checked and row ID is not in list of selected row IDs
    if(this.checked && index === -1){
        rows_selected.push(rowId);

        // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
    } else if (!this.checked && index !== -1){
        rows_selected.splice(index, 1);
    }

    if(this.checked){
        $row.addClass('selected');
    } else {
        $row.removeClass('selected');
    }

    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(table);

    // Prevent click event from propagating to parent
    e.stopPropagation();
});

// Handle click on table cells with checkboxes
$('#TableStock').on('click', 'tbody td, thead th:first-child', function(e){
    $(this).parent().find('input[type="checkbox"]').trigger('click');
});

// Handle click on "Select all" control
$('thead input[name="select_all"]', table.table().container()).on('click', function(e){
    if(this.checked){
        $('#TableStock tbody input[type="checkbox"]:not(:checked)').trigger('click');
    } else {
        $('#TableStock tbody input[type="checkbox"]:checked').trigger('click');
    }

    // Prevent click event from propagating to parent
    e.stopPropagation();
});

// Handle table draw event
table.on('draw', function(){
    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(table);
});

function ambilStock(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    if (rows_selected.length === 0) {
        $.alert({
            title: 'Peringatan!',
            content: 'Tidak ada data yang dipilih',
        });
    } else {
        $.ajax({
            method: 'POST',
            beforeSend: function (jqXHR, settings) {
                $.blockUI({
                    message: '<h1>Processing</h1>',
                    css: { border: '3px solid #a00' }
                });
            },
            data:{formData:rows_selected},
            url: href,
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
            success: function(response){
                $.unblockUI();
                //console.log(response);

                //window.location.replace(indexUrl);

                //buang data yang diceklis dari array object dataStocks
                dataStocks = $.grep(dataStocks, function(e){
                    //return e.id != id;
                    return !rows_selected.includes(e.id);
                });

                //reload tabel data stock
                table.clear();
                table.rows.add(dataStocks);
                table.draw();
                rows_selected = []; //kosongkan lagi list id ceklist

                //reload tabel data items
                dataItems = response;
                tableItem.clear();
                tableItem.rows.add(dataItems);
                tableItem.draw();
                rows_selected_item = []; //kosongkan lagi list id ceklist
                $('#TableItem tbody input[type="checkbox"]:checked').trigger('click');
            }
        });
    }
}


//Item----------------------------------------------------------------------------------------------------------------------
// Array holding selected row IDs
var rows_selected_item = [];
var tableItem = $('#TableItem').DataTable({
    searching: false,
    paging: false,
    ordering: false,
    sort: false,
    data: dataItems,
    columns: [
        { data: 'checkbox' },
        { data: 'qty_fmt' },
    ],
    'columnDefs': [{
        'targets': 0,
        'width': '1%',
        'className': 'dt-body-center',
        'render': function (data, type, full, meta){
            return '<input type="checkbox">';
        }
    }],
    'order': [[1, 'asc']],
    'rowCallback': function(row, data, dataIndex){
        // Get row ID
        var rowId = data.id;
        //console.log(rowId);

        // If row ID is in the list of selected row IDs
        if($.inArray(rowId, rows_selected_item) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
        }
    }
});

// Handle click on checkbox
$('#TableItem tbody').on('click', 'input[type="checkbox"]', function(e){
    var $row = $(this).closest('tr');

    // Get row data
    var data = tableItem.row($row).data();

    // Get row ID
    var rowId = data.id;

    // Determine whether row ID is in the list of selected row IDs
    var index = $.inArray(rowId, rows_selected_item);

    // If checkbox is checked and row ID is not in list of selected row IDs
    if(this.checked && index === -1){
        rows_selected_item.push(rowId);

        // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
    } else if (!this.checked && index !== -1){
        rows_selected_item.splice(index, 1);
    }

    if(this.checked){
        $row.addClass('selected');
    } else {
        $row.removeClass('selected');
    }

    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(tableItem);

    // Prevent click event from propagating to parent
    e.stopPropagation();
});

// Handle click on tableItem cells with checkboxes
$('#TableItem').on('click', 'tbody td, thead th:first-child', function(e){
    $(this).parent().find('input[type="checkbox"]').trigger('click');
});

// Handle click on "Select all" control
$('thead input[name="select_all"]', tableItem.table().container()).on('click', function(e){
    if(this.checked){
        $('#TableItem tbody input[type="checkbox"]:not(:checked)').trigger('click');
    } else {
        $('#TableItem tbody input[type="checkbox"]:checked').trigger('click');
    }

    // Prevent click event from propagating to parent
    e.stopPropagation();
});

// Handle tableItem draw event
tableItem.on('draw', function(){
    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(tableItem);
});

function kembalikanStock(event){
    event.preventDefault();
    var button = $(event.currentTarget);
    var href = button.attr('href');

    if (rows_selected_item.length === 0) {
        $.alert({
            title: 'Peringatan!',
            content: 'Tidak ada data yang dipilih',
        });
    } else {
        $.ajax({
            method: 'POST',
            beforeSend: function (jqXHR, settings) {
                $.blockUI({
                    message: '<h1>Processing</h1>',
                    css: { border: '3px solid #a00' }
                });
            },
            data:{formData:rows_selected_item},
            url: href,
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
            success: function(response){
                $.unblockUI();
                //console.log(response);

                //window.location.replace(indexUrl);

                //buang data yang diceklis dari array object dataStocks
                dataItems = $.grep(dataItems, function(e){
                    //return e.id != id;
                    return !rows_selected_item.includes(e.id);
                });

                //reload tabel data items
                tableItem.clear();
                tableItem.rows.add(dataItems);
                tableItem.draw();
                rows_selected_item = []; //kosongkan lagi list id ceklist

                //reload tabel data stock
                dataStocks = response;
                table.clear();
                table.rows.add(dataStocks);
                table.draw();
                rows_selected = []; //kosongkan lagi list id ceklist
                $('#TableStock tbody input[type="checkbox"]:checked').trigger('click');
            }
        });
    }
}