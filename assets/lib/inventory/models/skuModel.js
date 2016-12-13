'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partModel.js';
import './commodityModel.js';
import './unitTypeModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/sku';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'part',
    relatedModel: 'PartModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'commodity',
    relatedModel: 'CommodityModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'unitType',
    relatedModel: 'UnitTypeModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    name: null,
    number: null,
    label: null,
    part: null,
    commodity: null,
    unitType: null,
    isVoid: null,
    quantity: null,
    averageValue: null,
  },
});

globalNamespace.Models.SkuModel = Model;

export default Model;