'use strict';

import Marionette from 'marionette';
import ItemView from './searchableListItemView';
import NoChildrenView from 'lib/common/views/noChildrenView';
import Radio from 'backbone.radio';

export default Marionette.CollectionView.extend({
  childView: ItemView,
  emptyView: NoChildrenView,
  tagName: 'ul',
  className:'vsm-entity-list',
  childViewOptions(){
  	return {
  		searchPath: this.options.searchPath,
  		serializeData: this.options.serializeData
  	};
  }
});