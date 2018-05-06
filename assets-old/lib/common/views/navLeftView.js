"use strict";

import Marionette from 'marionette';

import viewTpl from './navLeftView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  className: 'nav-left',
});