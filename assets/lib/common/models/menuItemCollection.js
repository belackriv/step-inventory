'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './menuItemModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/menu_item';
  }
});