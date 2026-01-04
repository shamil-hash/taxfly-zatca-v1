function printPage(id)
{
    var html = "<html>";
    html += document.getElementById(id).innerHTML;
    html += "</html>";
    var printWin = window.open('', '', 'left=0,top=0,width=600,height=600,toolbar=0,scrollbars=0,status =0');
    
    // var printWin = window.open();
    printWin.document.write(html);
    printWin.document.close();
    printWin.focus();
    printWin.print();
    printWin.close();
    
}
