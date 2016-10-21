'use strict';

import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

import Model from './partModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/part';
  },
  selectOptionsUrl: '/select_options/part',
});