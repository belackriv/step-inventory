'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './userModel.js';

export default BaseUrlBaseCollection.extend({
  title: 'Users',
  model: Model,
  url(){
    return this.baseUrl+'/user';
  }
});