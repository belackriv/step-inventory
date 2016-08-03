"use strict";

import _ from 'underscore';
import Marionette from 'marionette';

import viewTpl from  "./propertyArrayListView.hbs!";

export default Marionette.View.extend({
  initialize(options){
    this.listenTo(options.model, 'change:'+options.propertyName, this.render);
  },
  template: viewTpl,
  ui: {
    'deleteButton': 'button[name="delete"]'
  },
  events: {
    'click @ui.deleteButton': 'removeElement'
  },
  serializeData(){
    return  {
      elements: this.model.get(this.options.propertyName),
      dictionary: this.options.dictionary
    };
  },
  removeElement(event){
    event.preventDefault();
    this.model['remove'+this.options.propertyName.capitalizeFirstLetter()](event.target.dataset.element);
  }
});
