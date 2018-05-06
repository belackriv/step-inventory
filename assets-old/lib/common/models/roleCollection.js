'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './roleModel.js';

export default BaseUrlBaseCollection.extend({
  title: 'Users',
  model: Model,
  url(){
    return this.baseUrl+'/role';
  }
});