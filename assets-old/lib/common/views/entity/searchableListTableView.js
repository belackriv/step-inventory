'use strict';

import Marionette from 'marionette';
import ChildView from './searchableListRowView.js';
import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';
import Radio from 'backbone.radio';

export default Marionette.CollectionView.extend({
  initialize(options){
    if(options.childViewOptions){
      this.childViewOptions = options.childViewOptions;
    }
  },
  tagName: 'tbody',
  childView: ChildView,
  emptyView: NoChildrenRowView,
  emptyViewOptions(){
    return {
    	colspan: this.options.colspan
    };
  }
});
