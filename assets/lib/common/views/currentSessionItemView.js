'use strict';

import Marionette from 'marionette';
import viewTpl from './currentSessionItemView.hbs!';

export default Marionette.View.extend({
  template: viewTpl,
  tagName: 'tr',
  ui:{
  	'closeSessionButton': 'button[data-ui="closeSession"]'
  },
  events:{
  	'click @ui.closeSessionButton': 'closeSession'
  },
  closeSession(event){
  	event.preventDefault();
  	if(this.confirmed){
	  	this.model.destroy().then(()=>{
	  		this.triggerMethod('session:destoyed');
	  	});
	  }else{
      this.confirmed = true;
      this.ui.closeSessionButton.text('Confirm?');
    }
  }
});
