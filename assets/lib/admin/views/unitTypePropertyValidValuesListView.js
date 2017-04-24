"use strict";

import Marionette from 'marionette';

import ItemView from './unitTypePropertyValidValuesItemView.js';

export default Marionette.CollectionView.extend({
    childView: ItemView,
    tagName: 'ul',
  });