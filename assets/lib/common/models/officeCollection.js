'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './officeModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/office';
  },
  parse(response){
    return response.list;
  }
});