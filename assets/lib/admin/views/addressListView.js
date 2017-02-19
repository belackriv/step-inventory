'use strict';

import Marionette from 'marionette';
import AddressItemView from './addressItemView.js';

export default Marionette.CollectionView.extend({
  childView: AddressItemView,
  tagName: 'ul',
  className: 'si-large-tag-list'
});
