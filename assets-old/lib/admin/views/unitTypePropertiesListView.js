"use strict";

import Marionette from 'marionette';

import ItemView from './unitTypePropertiesItemView.js';

export default Marionette.CollectionView.extend({
    childView: ItemView,
    tagName: 'ul',
  });