'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import ListView from './searchableListView.js';
import ListTableView from './searchableListTableLayoutView.js';
import PaginationWidgetView from './paginationWidgetView.js';
import viewTpl from './searchableListLayoutView.hbs!';
import Radio from 'backbone.radio';

export default Marionette.View.extend({
  initialize(options){
     this.collectionMode = (typeof this.options.usePagination === 'string')?this.options.usePagination:'client';
  },
  behaviors: {
    'Searchable': {},
  },
  template: viewTpl,
  regions: {
  	list: '.entity-list',
    paginationWidget: '.entity-pagination'
  },
  search(){
    this.triggerMethod('search');
  },
  onSearchComplete(collection){
    let viewOptions = _.extend(this.options, {
      collection: collection
    });
    if(this.options.useTableView){
      if(this.options.entityRowTpl){
        viewOptions.childViewOptions = viewOptions.childViewOptions?viewOptions.childViewOptions:{};
        viewOptions.childViewOptions = _.extend(
          viewOptions.childViewOptions,{
            template: this.options.entityRowTpl
        });
      }
      this.showChildView('list', new ListTableView(viewOptions));
    }else{
      this.showChildView('list', new ListView(viewOptions));
    }
    if(this.options.usePagination){
      this.showChildView('paginationWidget', new PaginationWidgetView({
        collection: collection,
      }));
    }
  },
  onRender(){
    if(this.collectionMode === 'client'){
      this.collection.on('fetch:done', this.search, this);
    }
    this.search();
  },
  onChildviewSelectModel(childView, args){
    this.triggerMethod('select:model', childView, {
      model: childView.model
    });
  },
  onChildviewButtonClick(childView, args){
    this.triggerMethod('button:click', childView, {
      model: childView.model,
      button: args.button
    });
  },
  onDestroy(){
    this.collection.off(null, this.search, this);
  }
});