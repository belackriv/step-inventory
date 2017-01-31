"use strict";

import viewTpl from  "./defaultView.hbs!";
import Marionette from 'marionette';


export default Marionette.View.extend({
  template: viewTpl,
  modelEvents:{
  	'change': 'render'
  },
  onRender(){
  	let test;
  },
});