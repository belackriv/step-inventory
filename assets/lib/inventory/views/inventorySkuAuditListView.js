'use strict';

import Marionette from 'marionette';
import ChildView from './inventorySkuAuditItemView.js';
import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';

export default Marionette.CollectionView.extend({
  childView: ChildView,
  tagName: 'tbody',
  emptyView: NoChildrenRowView,
});
