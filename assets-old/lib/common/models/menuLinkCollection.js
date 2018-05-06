'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './menuLinkModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/menu_link';
  }
});