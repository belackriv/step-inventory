'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './planModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/plan';
  },
});