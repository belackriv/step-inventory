'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './subscriptionModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/subscription';
  },
});