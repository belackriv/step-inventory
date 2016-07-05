'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

export default Marionette.Behavior.extend({
  ui: {
    'form': 'form',
    'cancelButton': 'button.cancel-button',
    'deleteButton': 'button.delete-button',
  },
  events: {
    'submit @ui.form': 'save',
    'click @ui.cancelButton': 'cancel',
    'click @ui.deleteButton': 'delete',
  },
  save(event){
    event.preventDefault();
    this.view.model.save();
    this.view.triggerMethod('add:entity', this.view);
    this.view.triggerMethod('show:list', this.view, {
      view: this,
      model:this.model,
    });
  },
  cancel(event){
    event.preventDefault();
    this.view.model.set(this.previousAttributes);
    this.view.triggerMethod('show:list');
  },
  delete(){
    if(this.ui.deleteButton.data('confirm')){
      this.view.model.destroy();
      this.view.triggerMethod('show:list');
    }else{
      this.ui.deleteButton.text('Confirm?').data('confirm', true);
    }
  },
});