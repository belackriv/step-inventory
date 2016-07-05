'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import ListView from './searchableListView';
import ListTableView from './searchableListTableLayoutView';
import PaginationWidgetView from './paginationWidgetView';
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
  onDestroy(){
    this.collection.off(null, this.search, this);
  }
});