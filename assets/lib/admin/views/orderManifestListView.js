"use strict";

import Marionette from 'marionette';

import ItemView from './orderManifestItemView.js';

export default Marionette.CollectionView.extend({
    childView: ItemView,
    tagName: 'tbody',
  });