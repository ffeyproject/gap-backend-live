function printDiv(div) {
    var fontSize = document.getElementById("SizeText").value;

    var divContents = document.getElementById(div).innerHTML;
    //var a = window.open('', '', 'height=500, width=500');
    var a = window.open('', '');
    a.document.write('<html>');
    a.document.write('<head>');
    a.document.write('<style type="text/css">');
    a.document.write('body{font-size:' + fontSize + 'px; letter-spacing: 2px;} table {font-size:' + fontSize + 'px; border-spacing: 0; letter-spacing: 0em; width:100%;} th, td {padding: 0.2em 0.2em;} td{vertical-align: top;} .table-bordered td, .table-bordered th, .bordered{border: 1px solid black;}');
    //a.document.write('@media print {html, body {width: 5.5in; /* was 8.5in */ height: 8.5in; /* was 5.5in */ display: block; font-family: "Calibri"; /*font-size: auto; NOT A VALID PROPERTY */} @page {size: 5.5in 8.5in /* . Random dot? */;}}');
    a.document.write('</style>');
    a.document.write('</head>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}