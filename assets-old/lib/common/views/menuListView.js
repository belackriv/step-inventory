"use strict";

import Marionette from 'marionette';

import MenuItemView from './menuItemView.js';

export default Marionette.CollectionView.extend({
    childView: MenuItemView,
    tagName: 'ul',
    className: 'menu-list',
  });