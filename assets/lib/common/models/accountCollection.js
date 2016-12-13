'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './accountModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/account';
  },
});