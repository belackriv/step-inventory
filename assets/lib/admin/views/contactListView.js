'use strict';

import Marionette from 'marionette';
import ContactItemView from './contactItemView.js';

export default Marionette.CollectionView.extend({
  childView: ContactItemView,
  tagName: 'ul',
});
