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
    return {
      template: this.options.childTemplate
    }
  }
});
