'use strict';

import Marionette from 'marionette';
import SearchableListLayoutView from './searchableListLayoutView';
import viewTpl from './selectableLayoutView.hbs!';
import Radio from 'backbone.radio';

export default Marionette.View.extend({
  template: viewTpl,
  regions: {
  	selectionPane: '.entity-selection-pane'
  },
  ui: {
    'isLoadingContainer': '.is-loading-container'
  },
  modelEvents: {
     "change:isLoading": 'isLoadingChanged'
  },
  onBeforeShow(){
    this.showChildView('selectionPane', new SearchableListLayoutView({
      collection: this.collection,
      searchPath: this.options.searchPath,
      serializeData: this.options.serializeData,
    }));
  },
  onChildviewSelectModel(childView, triggeringView, args){
    this.options.selectHandler(triggeringView, args);
  },
  isLoadingChanged(){
    if(this.model.get('isLoading')){
      this.ui.isLoadingContainer.show()
    }else{
      this.ui.isLoadingContainer.hide();
    }
  },
});