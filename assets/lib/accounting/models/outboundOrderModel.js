'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/inventory/models/travelerIdModel';
import './customerModel';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/outbound_order';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'customer',
    relatedModel: 'CustomerModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    label: null,
    customer: null,
    description: null,
    isVoid: false,
    salesItems: null,
  },
});

globalNamespace.Models.OutboundOrderModel = Model;

export default Model;