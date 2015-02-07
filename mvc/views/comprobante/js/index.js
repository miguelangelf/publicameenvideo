function printVoucher(themeDomain){
    var prtContent = document.getElementById("voucher-container");
    var printer   = window.open('', '', 'letf=0,top=0,width=990,height=600,toolbar=0,scrollbars=1,status=0');
    var css = '<link rel="stylesheet" href="'+themeDomain+'css/bootstrap.min.css">';
        css += '<link rel="stylesheet" href="'+themeDomain+'css/jquery-ui.css">';
        css += '<link rel="stylesheet" href="'+themeDomain+'css/font-awesome.css">';
        css += '<link rel="stylesheet" href="'+themeDomain+'php/general.css">';
        css += '<style>#btn-print-comp,.title-comprobante{display:none;}</style>'
    printer.document.write(css+prtContent.innerHTML);
    printer.document.close();
    printer.focus();
    printer.print();
    printer.close();
}
