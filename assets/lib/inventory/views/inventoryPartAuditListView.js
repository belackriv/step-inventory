'use strict';

import Marionette from 'marionette';
import ChildView from './inventoryPartAuditItemView.js';
import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  tagName: 'tbody',
  emptyView: NoChildrenRowView,
});
