'use strict';

import viewTpl from './unitPropertyItemView.hbs!';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'li',
  className: 'control is-horizontal'
});
