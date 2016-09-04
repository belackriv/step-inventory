'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/inboundOrderModel';
import 'lib/accounting/models/outboundOrderModel';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/mass_tid';
  },
  relations: [{
    type: Backbone.HasMany,
    key: 'travelerIds',
    relatedModel: 'TravelerIdModel',
    includeInJSON: true,
  }],
  defaults: {
    travelerIds: null,
    //only for client for now
    serials: null,
    serialsArray: null,
    count: null,
  },
});

globalNamespace.Models.MassTravelerIdModel = Model;

export default Model;