'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './organizationModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/organization';
  },
});