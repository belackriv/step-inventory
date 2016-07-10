'use strict';

import Marionette from 'marionette';
import ChildView from './userRoleItemView';
import NoChildrenRowView from 'lib/common/views/noChildrenRowView';
import Radio from 'backbone.radio';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  emptyView: NoChildrenRowView,
  tagName: 'ul'
});
