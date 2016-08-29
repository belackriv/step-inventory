'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/inventory/models/travelerIdModel';
import './clientModel';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inbound_order';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'client',
    relatedModel: 'ClientModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    label: null,
    client: null,
    description: null,
    isVoid: false,
    travelerIds: null,
  },
});

globalNamespace.Models.InboundOrderModel = Model;

export default Model;