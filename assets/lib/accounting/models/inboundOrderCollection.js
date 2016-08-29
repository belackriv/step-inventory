'use strict';

import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

import Model from './inboundOrderModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/inbound_order';
  },
});