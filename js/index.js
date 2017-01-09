'use strict';

var t = require('./tabs');
var form = require('./form');
var tabs = document.getElementById('tab-group');

t.onClick(tabs);
form.submit(tabs);
