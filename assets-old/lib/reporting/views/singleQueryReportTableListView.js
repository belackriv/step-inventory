'use strict';

import Marionette from 'marionette';
import Radio from 'backbone.radio';
import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';
import ChildView from './singleQueryReportTableRowView.js';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  emptyView: NoChildrenRowView,
  tagName: 'tbody',
  emptyViewOptions(){
    let colspan = (this.options.columns instanceof Array)?this.options.columns.length:0;
  	return {
      colspan: colspan,
    };
	},
  childViewOptions(){
    return {
      columns: this.options.columns
    };
  },
  onChildviewSelectModel(childView, args){

  }
});
