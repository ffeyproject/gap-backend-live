$("#trnsclocalform-pmt_term").on('change.yii', function(){
    let pmtTerm = $(this).val();
    console.log(pmtTerm);
    let date = new Date();
    date.setDate(date.getDate() + parseInt(pmtTerm));
    console.log(date);
    let year = pad(date.getFullYear());
    let month = pad(date.getMonth()+1);
    let day = pad(date.getDate());
    let dateFmt = year + '-' + month + '-' + day;
    console.log(dateFmt);

    //$('input[name="TrnScLocalForm[due_date]"]').val(dateFmt);
    $("#trnsclocalform-due_date-kvdate").kvDatepicker('update', date);
});

function pad(numb) {
    return(numb < 10 ? '0' : '') + numb;
}