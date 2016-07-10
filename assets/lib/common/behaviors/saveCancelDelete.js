'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

export default Marionette.Behavior.extend({
  initialize(){
    this.model = this.view.options.model;
    this.setPreviousAttributes();
    this.listenTo(this.model, 'change:id', this.setPreviousAttributes);
  },
  ui: {
    'form': 'form',
    'saveButton': 'button[data-ui-name=save]',
    'cancelButton': 'button[data-ui-name=cancel]',
    'deleteButton': 'button[data-ui-name=delete]',
  },
  events: {
    'submit @ui.form': 'save',
    'click @ui.saveButton': 'save',
    'click @ui.cancelButton': 'cancel',
    'click @ui.deleteButton': 'delete',
  },
  setPreviousAttributes(){
    this.previousAttributes = _.clone(this.model.attributes);
  },
  disableFormButtons(){
    this.ui.saveButton.addClass('is-disabled').prop('disable', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disable', true);
    this.ui.deleteButton.addClass('is-disabled').prop('disable', true);
  },
  enableFormButtons(){
    this.ui.saveButton.removeClass('is-disabled').prop('disable', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disable', false);
    this.ui.deleteButton.removeClass('is-disabled').prop('disable', false);
  },
  save(event){
    event.preventDefault();
    this.disableFormButtons();
    this.view.model.save().always(()=>{
      this.enableFormButtons();
    }).done(()=>{
      this.view.triggerMethod('add:entity', this.view);
      this.view.triggerMethod('show:list', this.view, {
        view: this,
        model:this.model,
      });
    });
  },
  cancel(event){
    event.preventDefault();
    this.view.model.set(this.previousAttributes);
    this.view.triggerMethod('show:list');
  },
  delete(){
    if(this.ui.deleteButton.data('confirm')){
      this.disableFormButtons();
      this.view.model.destroy().always(()=>{
        this.enableFormButtons();
      }).done(()=>{
        this.view.triggerMethod('show:list', this.view, {
          view: this,
          model:this.model,
        });
      });
    }else{
      this.ui.deleteButton.text('Confirm?').data('confirm', true);
    }
  },
});