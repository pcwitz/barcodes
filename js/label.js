'use strict';

var dymo = require('./dymo');
var date = require('../../common/date');
var data = require('../../common/data');

exports.print = function(claim, userName) {

  function _send(xml) {
    try {
      var labelXml = xml.responseText;
      var label = dymo.label.framework.openLabelXml(labelXml);
      // ready label variables
      var barcode = '';
      var claimInfo = '';
      if (!claim.hasOwnProperty('clmkey')) {
        // handle sis label which has different claim info matter and no barcode
        var h = '-';
        claimInfo = claim.occonr+h+claim.ocprfx+h+claim.ocplnr+h+claim.occmnr + '    ' + claim.name;
      } else {
        barcode = '777' + claim.clmkey.replace(/-/g, ''); // trim hyphens
        claimInfo = claim.clmkey + '   ' + claim.adj + '    ' + claim.name;
      }
      var stamp = date.slashes();
      var dateUser = 'Received:   ' + stamp + '   MRP:  ' + userName;

      // set label text
      label.setObjectText('Barcode', barcode);
      label.setObjectText('ClaimInfo', claimInfo);
      label.setObjectText('DateUser', dateUser);

      // select printer to print on
      // for simplicity sake just use the first LabelWriter printer
      var printers = dymo.label.framework.getPrinters();
      if (printers.length ===0) {
        var version = dymo.label.framework.VERSION;
        throw 'No printers detected. Install DYMO software supported by DYMO Label Framework version ' + version + '.';
      }

      var printerName = '';
      for (var i = 0; i < printers.length; ++i) {
        var printer = printers[i];
        if (printer.printerType ==='LabelWriterPrinter') {
          printerName = printer.name;
          break;
        }
      }

      if (printerName ==='') {
        throw 'No LabelWriter printers found. Install LabelWriter printer.';
      }

      // finally print the label
      label.print(printerName);
    } catch(e) {
      var p = document.getElementById('message');
      p.textContent = e.message || e;
    }
  }

  data.reqListener('barcode.xml', function() {
    _send(this);
  });

}; // end dymo-label click event handler