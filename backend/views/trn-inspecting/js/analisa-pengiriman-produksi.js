function printDiv(div) {
    let fontSize = document.getElementById("SizeText").value;

    let divContents = document.getElementById(div).innerHTML;
    //let a = window.open('', '', 'height=500, width=500');
    let a = window.open('', '');
    a.document.write('<html lang="id-ID">');
    a.document.write('<head>');
    a.document.write('<title>Analisa Pengiriman Produksi</title>');
    a.document.write('<style>');
    a.document.write('body{font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 2px;} th, td {padding: 0.5em 1em;}');
    //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
    a.document.write('</style>');
    a.document.write('</head>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}