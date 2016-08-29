'use strict';

import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

import Model from './clientModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/client';
  },
});