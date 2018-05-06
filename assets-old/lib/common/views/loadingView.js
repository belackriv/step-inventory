"use strict";

import viewTpl from  "./loadingView.hbs!";
import Marionette from 'marionette';


export default Marionette.View.extend({
  template: viewTpl,
});