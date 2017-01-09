'use strict';

exports.onClick = function(tabs) {
  var h = 'tab-header', c = 'tab-content';

  function deactivateAllTabs() {
    var  tabHeaders = document.getElementsByClassName(h),
         tabContent = document.getElementsByClassName(c);

    for (var i = 0; i < tabHeaders.length; i++) {
      tabHeaders[i].className = h;
      tabContent[i].className = c;
    }
  }

  tabs.addEventListener('click', function(event) {
    event.stopPropagation();
    var t = event.target; //either header or text span
    if (t.className !== h && t.className !== 'count') return;
    var header;
    if (t.className === 'count') {
      header = t.parentElement;
    } else {
      header = t;
    }
    // 'this' is what heard the event (i.e. tabHeader)
    var headerId = header.id, // e.g., tab-header-1
        contentId = headerId.replace('header', 'content');
    deactivateAllTabs();
    header.className = h + ' active';
    document.getElementById(contentId).className = c + ' active';
  });

};