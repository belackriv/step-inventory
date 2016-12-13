"use strict";

import 'bulma/css/bulma.css!';
import './globalNamespace';
import 'backbone.stickit';
import 'lib/shims/marionette.stickit.shim';
import util from 'lib/util.js';
import App from 'lib/app.js';
import 'lib/common/behaviors/behaviors.js';
import 'lib/common/handlebarsHelpers/helpers.js';

var app = new App();
app.start();