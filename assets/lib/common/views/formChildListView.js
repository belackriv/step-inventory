'use strict';

import Marionette from 'marionette';
import ChildView from './formChildItemView.js';
import NoChildrenRowView from './noChildrenRowView.js';
import Radio from 'backbone.radio';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  emptyView: NoChildrenRowView,
  tagName: 'ul',
  childViewOptions(model, index){
    if(this.collection.length > 0){
      return {
        template: this.options.childTemplate,
        noDelete: this.options.noDelete
      };
    }else{
      return {
        noDelete: this.options.noDelete
      };
    }
  }
});
