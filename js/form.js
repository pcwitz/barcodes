'use strict';

var cookie = require('../../common/cookie');
var data = require('../../common/data');
var dom = require('../../common/dom');
var spinner = require('../../common/spinner');
var label = require('./label');

function _parse(form) {
  // create an object of user inputs = {nameAttribute: value}
  var inputs = {};
  for (var i = 0; i < form.length; i++) {
    if(form.elements[i].value !== '') {
      var n = form.elements[i].attributes.getNamedItem('name').value;
      var v = form.elements.namedItem(n).value;
      inputs[n] = v;
    }
  }
  return inputs;
}

exports.submit = function(tabs) {

  var deregisterTabsListener = function () {};

  var f = document.getElementById('form');
  f.addEventListener('submit', function(event) {
    event.preventDefault();
    // resets
    deregisterTabsListener();
    var p = document.getElementById('message');
    p.textContent = '';
    var recentTab = document.getElementById('recent-tab');
    recentTab.textContent = 'NONE';
    var allTab = document.getElementById('all-tab');
    allTab.textContent = 'NONE';
    var sisTab = document.getElementById('sis-tab');
    sisTab.textContent = 'NONE';

    var api = 'http://ahcsdev.ibx.com/claimweb/api/barcodes/index.php?callback=?';
    var inputs = _parse(f);

    if (Object.keys(inputs).length === 0) {
      p.textContent = 'At least one field required!';
    } else {
      // var tabs = document.getElementById('tab-group');
      tabs.style.display = 'none';
      spinner.start('form');

      var userName = cookie.getByName('MyClaimWebUserName');
      inputs.userId = userName; // add user info to get office number

      data.jacks(api, inputs, function(res) {
         function iconic(event) {
          event.stopPropagation();
          var cur = event.target;
          if ((cur.className === 'icon-barcode' ) || (cur.className === 'icon-label')) {
            var td = cur.parentNode;
            var id = td.getAttribute('id');
            // we need to know which table/array (recent, all, or sis) the barcode belongs too 
            var tableId = '';
            var table = dom.getNearestAncestorByTagName(td, 'table');
            if (table) {
              tableId = table.getAttribute('id');
            }
            // var thang = res[Object.keys(res).length - 1];
            // res is an object (not an array) containing 3 arrays
            var claim = res[tableId][id]; // claim is an object and not an array;
            label.print(claim, userName);
          }
        } // end icon click callback function
        spinner.stop();
        tabs.style.display = 'block';

        if (res.error) {
          var p = document.getElementById('message');
          p.textContent = res.error;
        } else {
          var headers = [
            'Claim &numero;',
            'Name',
            'DOI',
            'Status',
            'Adjuster',
            'Code',
            'Ext',
            'Office',
            'Nurse',
            'DOB',
            'Barcode'
          ];
          var button = '<div title="print barcode" class="icon-barcode"></div>';
          var tabContent1 = document.getElementById('tab-content-1');
          if (res.recent) { // we got recent results and can activate new tab
            var options = {
              id: 'recent',
              class: 'full striped',
              td: button
            };
            recentTab.textContent = res.recent.length;
            tabContent1.innerHTML = dom.makeTable(res.recent, headers, options);
          } else {
            tabContent1.innerHTML = '<h3>No results</h3>';
          }

          var tabContent2 = document.getElementById('tab-content-2');
          if (res.all) { // we got recent results and can activate old tab
            allTab.textContent = res.all.length;
            var options2 = {
              id: 'all',
              class: 'full striped',
              td: button
            };
            tabContent2.innerHTML = dom.makeTable(res.all, headers, options2);
          } else {
            tabContent2.innerHTML = '<h3>No results</h3>';
          }

          var tabContent3 = document.getElementById('tab-content-3');

          if (res.sis) { // we got recent results and can activate sis tab
            sisTab.textContent = res.sis.length;
            var headersSis = [
              'Company',
              'Prefix',
              'Policy',
              'Branch',
              'Claim &numero;',
              'Name',
              'Loss Date',
              'Label'
            ];
            var optionsSis = {
              id: 'sis',
              class: 'full striped',
              td: '<div title="print label" class="icon-label"></div>'
            };
            tabContent3.innerHTML = dom.makeTable(res.sis, headersSis, optionsSis);
          } else {
            tabContent3.innerHTML = '<h3>No results</h3>';
          }

          tabs.addEventListener('click', iconic, false);
          deregisterTabsListener = function () {
            tabs.removeEventListener('click', iconic);
          };
        }
        f.reset(); // clear form values
      }); // end api response handler
    } // end else with at least one form field with data
  }); // end form submit event listener
}; // end submit function