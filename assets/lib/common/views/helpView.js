"use strict";

import Marionette from 'marionette';

import viewTpl from './helpView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  className: 'panel si-help-panel',
});