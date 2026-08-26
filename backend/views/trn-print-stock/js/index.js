function printDivPL(div) {
    var fontSize = document.getElementById("SizeText") ? document.getElementById("SizeText").value : 11;
    var divElement = document.getElementById(div);
    var divContents = divElement.innerHTML;

    var a = window.open('', '_blank');
    a.document.write('<!DOCTYPE html><html><head><title>Print Lembar Palet</title>');
    
    // Copy all style tags from current document to print window
    var styles = document.getElementsByTagName('style');
    for (var i = 0; i < styles.length; i++) {
        a.document.write(styles[i].outerHTML);
    }

    a.document.write('<style type="text/css">');
    a.document.write('@page { size: auto; margin: 0mm; }');
    a.document.write('body { margin: 5mm !important; font-family: "Courier New", Courier, monospace, Arial, sans-serif !important; }');
    a.document.write('.palet-wrapper { font-size: ' + fontSize + 'px !important; }');
    a.document.write('</style>');
    a.document.write('</head><body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    
    setTimeout(function() {
        a.print();
        a.close();
    }, 250);
}