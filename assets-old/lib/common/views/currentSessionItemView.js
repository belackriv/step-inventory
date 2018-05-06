'use strict';

import _ from 'underscore';
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
  serializeData(){
    let data = _.clone(this.model.attributes);
    data.rowNum = this.options.parentCollection.indexOf(this.model) + 1;
    return data;
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
