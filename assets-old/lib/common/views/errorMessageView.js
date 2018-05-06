'use strict';

import Marionette from 'marionette';
import viewTpl from './errorMessageView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
});