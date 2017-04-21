"use strict";

import viewTpl from  "./defaultView.hbs!";
import Marionette from 'marionette';


export default Marionette.View.extend({
  template: viewTpl,
  modelEvents:{
  	'change': 'render'
  },
  ui:{
  	'dismissAlertButton': 'button.delete[data-log-id]'
  },
  events:{
  	'click @ui.dismissAlertButton': 'dismissAlert'
  },
  onRender(){

  },
  dismissAlert(event){
  	let logId = parseInt(event.target.dataset.logId);
  	let model = this.model.get('inventoryAlertLogs').find({id: logId});
  	let parent = event.target.parentNode;
  	let loadingIcon = document.createElement('i');
  	loadingIcon.classList.add('fa', 'fa-spinner', 'fa-pulse', 'is-pulled-right');
  	parent.replaceChild(loadingIcon, event.target);
  	model.dismissAlert().then(()=>{
  		parent.remove();
  	});
  }
});