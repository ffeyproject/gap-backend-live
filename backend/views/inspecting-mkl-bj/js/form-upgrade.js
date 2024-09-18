const formatterNumber = new Intl.NumberFormat('ID-id', {style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 8});

var itemTable = $('#InspectingItemTable').DataTable({
    data: inspectingItems,
    columns: [
        { "data": function (row, type, set) {return "-";}},
        { "data": "gradeLabel" },
        { "data": "gradeupLabel" },
        { "data": "qty" },
        { "data": "join_piece" },
        { "data": "lot_no" },
        { "data": "defect" },
        { "data": "note" },
        {"data": function (row, type, set) {return '<button class="btn btn-xs btn-warning editItemData"><i class="fa fa-edit"></i></button>';}},
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
    columnDefs: [
        {
            "render": function (data, type, row) {
                //return moment(data, "X").format("Do MMMM YYYY, h:mm");
                return formatterNumber.format(data);
            },
            "targets": [3]
        },
    ],
    rowCallback: function(row, data, index) {
        $('td', row).eq(0).html(index +1);
    }
});

$('#InspectingItemTable tbody').on( 'click', 'button.editItemData', function () {
    let row = itemTable.row( $(this).parents('tr'));
    let rowNumber = row.index(); //for check the index of row that you want to edit in table
    let data = row.data();
    let gradeOld = data.grade;
    let gradeOldLabel = data.gradeLabel;

    // value edit
    let jp_value = data.join_piece != null ? data.join_piece : '';
    let d_value = data.defect != null ? data.defect : '';
    let ln_value = data.lot_no != null ? data.lot_no : '';
    let q_value = data.qty != null ? data.qty : '';
    //console.log(data);

    $.confirm({
        title: 'Upgrade!',
        content: '' +
            '<form action="" class="formName">' +
                '<div class="form-group"><label>Upgrade</label><select id="optionGrade" class="editGrade form-control"><option value="7">Grade A+</option> <option value="8">Grade A*</option> <option value="3">Grade C</option> <option value="4">Piece Kecil</option> <option value="5">Sample</option> <option value="1">Grade A</option> <option value="2">Grade B</option></select></div>' +
                '<div class="form-group"><label>Ukuran</label><input type="text" class="editUkuran form-control" value="' + q_value +'"/></div>' +
                '<div class="form-group"><label>Join Piece</label><input type="text" class="editJoinPiece form-control" value="' + jp_value +'"/></div>' +
                '<div class="form-group"><label>Lot No</label><input type="text" class="editLotNo form-control" value="' + ln_value +'"/></div>' +
                '<div class="form-group"><label>Defect</label><input type="text" class="editDefect form-control" value="' + d_value +'"/></div>' +
                '<div class="form-group"><label>Keterangan</label><input type="text" class="editKeterangan form-control" value="' + data.note +'"/></div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue',
                action: function () {
                    let grade = $("#optionGrade").children("option:selected").val();
                    let gradeLabel = $("#optionGrade").children("option:selected").text();
                    let defect = this.$content.find('.editDefect').val();
                    let lot_no = this.$content.find('.editLotNo').val();
                    let qty = this.$content.find('.editUkuran').val();
                    let joinPiece = this.$content.find('.editJoinPiece').val();
                    let note = this.$content.find('.editKeterangan').val();

                    if(!grade){
                        $.alert('Grade harus diisi.');
                        return false;
                    }
                    if(!qty){
                        $.alert('Ukuran harus diisi.');
                        return false;
                    }

                    //console.log({grade: grade, gradeLabel: gradeLabel, ukuran: ukuran, join_piece: joinPiece, keterangan: keterangan});
                    itemTable.row(rowNumber).data(
                        {
                            id:data.id,
                            grade: gradeOld,
                            gradeLabel: gradeOldLabel,
                            grade_up: grade,
                            gradeupLabel: gradeLabel,
                            qty: qty,
                            join_piece: joinPiece == 'null' ? '' : joinPiece,
                            lot_no: lot_no,
                            defect: defect,
                            note: note
                        }
                    ).draw(false);
                }
            },
            cancel: function () {
                //close
            },
        },
        onContentReady: function () {
            // bind to events
            var jc = this;

            this.$content.find('option[value="'+gradeOld+'"]').attr('selected','selected');

            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.$$formSubmit.trigger('click'); // reference the button and click it
            });
        }
    });
});

$('#InspectingFormHeader').on('afterInit', function (e) {
    $('#InspectingFormHeader').on('beforeSubmit', function () {
        var $yiiform = $(this);

        let dataItems = itemTable.rows().data().toArray();

        // console.log(dataItems);

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
                    console.log(data);
                    if(data.success) {
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
});

$("#BtnSubmitForm").click(function (){
    $("#InspectingFormHeader").submit();
});

// ini remove gais
$('#InspectingItemTable tbody').on( 'click', 'button.removeItemData', function () {
    var row = itemTable.row( $(this).parents('tr') );
    row.remove().draw(false);
    $("#ItemCounter").html(itemTable.rows().data().length);
});

// ini store new data
$('#InspectingFormItem').on('afterInit', function (e) {
    $('#InspectingFormItem').on('beforeSubmit', function () {
        let data = {
            id: null,
            grade: $('#inspectingmklbjitems-grade').select2('data')[0].id,
            gradeLabel: $('#inspectingmklbjitems-grade').select2('data')[0].text,
            defect: $('#inspectingmklbjitems-defect').val(),
            lot_no: $('#inspectingmklbjitems-lot_no').val(),
            qty: $('#InspectingFormItem').yiiActiveForm('find', 'inspectingmklbjitems-qty').value,
            join_piece: $('#InspectingFormItem').yiiActiveForm('find', 'inspectingmklbjitems-join_piece').value,
            note: $('#InspectingFormItem').yiiActiveForm('find', 'inspectingmklbjitems-note').value,
        };
        //console.log(data);
        itemTable.row.add(data).draw(false);

        $("#ItemCounter").html(itemTable.rows().data().length);

        $("#InspectingFormItem").get(0).reset();

        $( "#inspectingmklbjitems-qty").focus();

        return false; // prevent default form submission
    });
});