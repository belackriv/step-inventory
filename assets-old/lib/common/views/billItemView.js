'use strict';

import Marionette from 'marionette';
import viewTpl from './billItemView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
});
