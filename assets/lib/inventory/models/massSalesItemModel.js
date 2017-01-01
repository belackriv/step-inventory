'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/accounting/models/outboundOrderModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/mass_sales_item';
  },
  relations: [{
    type: BackboneRelational.HasMany,
    key: 'salesItems',
    relatedModel: 'SalesItemModel',
    includeInJSON: true,
  }],
  defaults: {
    salesItems: null,
  },
});

globalNamespace.Models.MassSalesItemModel = Model;

export default Model;