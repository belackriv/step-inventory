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
    'button': 'button',
    'link': 'a[data-ui-link]',
  },
  events: {
    'click @ui.button': 'triggerButtonClick',
    'click @ui.link': 'triggerLinkClick'
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'button:click': 'triggerChildButtonClick',
    'link:click': 'triggerChildLinkClick'
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
    event.stopPropagation();
    this.triggerMethod('button:click', this, {
      model: this.model,
      button: event.target
    });
  },
  triggerLinkClick(event){
    event.preventDefault();
    event.stopPropagation();
    this.triggerMethod('link:click', this, {
      model: this.model,
      link: event.target
    });
  },
  triggerChildButtonClick(childView, args){
    this.triggerMethod('button:click', childView, {
      model: childView.model,
      button: args.button
    });
  },
  triggerChildLinkClick(childView, args){
    this.triggerMethod('link:click', childView, {
      model: childView.model,
      link: args.link
    });
  },
});