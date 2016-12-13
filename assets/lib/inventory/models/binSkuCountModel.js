'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './skuModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/bin_sku_count';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    bin: null,
    sku: null,
    count: null
  },
});

globalNamespace.Models.BinSkuCountModel = Model;

export default Model;