var itemTable = $('#ItemsTable').DataTable({
    data: [],
    columns: [
        //{ "data": function (row, type, set) {return "-";}},
        { "data": "id" },
        { "data": "motif" },
        { "data": "grade_name" },
        { "data": "qty_fmt" },
        { "data": "unit_name" },
        //{"data": function (row, type, set) {return '<button class="btn btn-xs btn-danger removeItemData"><i class="fa fa-trash"></i></button>';}},
    ],
    ordering: false,
    responsive: true,
    paging: false,
    searching: false,
    info: false,
    /*rowCallback: function(row, data, index) {
        $('td', row).eq(0).html(index +1);
    }*/
});

function addItem(e, data){
    e.preventDefault();
    //console.log(data);

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

$("#BtnJual").click(function(){
    let data = itemTable.rows().data();
    let dataItems = [];
    //console.log(data);

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
        qtyArr.push(qtys[qty].qty + " " + qtys[qty].unit)
    }

    let qtyArrStr = qtyArr.join(" + ");

    /*console.log("greigesStr: ");console.log(greigesStr);
    console.log('qtyArrStr: ');console.log(qtyArrStr);
    console.log('woBuyers: ');console.log(woBuyers);*/

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

                        //console.log("dataPost: ");console.log(dataPost);

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
                                            //console.log(data);
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
                            // Transforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: data.results
                            };
                        }
                        //dataType: 'json'
                        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
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
            content: 'Tidak ada data.',
        });
    }
});
