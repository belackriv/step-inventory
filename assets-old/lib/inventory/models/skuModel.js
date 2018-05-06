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
  importData: {
    type: 'sku',
    properties: [
      { name: 'name', required: true, description: null},
      { name: 'number', required: true, description: null},
      { name: 'label', required: true, description: null},
      { name: 'supplierCode', required: false, description: 'Integer, Must be an existing Id'},
      { name: 'supplierSku', required: false, description: 'Integer, Must be an existing Id'},
      { name: 'part', required: false, description: 'Integer, Must be an existing Id. If supplied, cannot have a commodity or unitType.'},
      { name: 'commodity', required: false, description: 'Integer, Must be an existing Id. If supplied, cannot have a part or unitType.'},
      { name: 'unitType', required: false, description: 'Integer, Must be an existing Id. If supplied, cannot have a part or commodity.'},
      { name: 'isVoid', required: true, description: 'Boolean'},
      { name: 'quantity', required: true, description: 'Integer.  This is how much of a thing(part, commodity, or units) there is for this sku.'},
    ]
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
    supplierCode: null,
    supplierSku: null,
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