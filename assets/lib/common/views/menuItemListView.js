"use strict";

import globalNamespace from 'lib/globalNamespace.js';
import Marionette from 'marionette';

export default Marionette.CollectionView.extend({
  childView(item){
    return globalNamespace.Views.MenuItemView;
  },
  tagName: 'ul',
});