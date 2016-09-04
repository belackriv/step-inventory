'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import SearchableListTableView from './searchableListTableView';


export default Marionette.View.extend({
  initialize(options){
    if(typeof options.template === 'function'){
      this.template = options.template;
    }else if(typeof options.entityListTableLayoutTpl === 'function'){
      this.template = options.entityListTableLayoutTpl;
    }
    if(!this.options.childViewOptions){
      this.options.childViewOptions = {};
    }
    this.options.childViewOptions.searchPath =  this.options.searchPath;
    this.options.childViewOptions.serializeData = this.options.serializeData;
  },
  regions: {
    tbody: {
      el: 'tbody',
      replaceElement: true
    },
  },
  ui:{
    'button': 'button'
  },
  events: {
    'click @ui.button': 'triggerButtonClick'
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'button:click': 'triggerChildButtonClick'
  },
  onRender(){
    this.showChildView('tbody', new SearchableListTableView({
      collection: this.collection,
      childViewOptions: this.options.childViewOptions,
      colspan: this.options.colspan
    }));
  },
  selectModel(childView, args){
    this.triggerMethod('select:model', childView, {
      model: childView.model
    });
  },
  triggerButtonClick(event){
    event.preventDefault();
    this.triggerMethod('button:click', this, {
      model: this.model,
      button: event.target
    });
  },
  triggerChildButtonClick(childView, args){
    this.triggerMethod('button:click', childView, {
      model: childView.model,
      button: args.button
    });
  },
});