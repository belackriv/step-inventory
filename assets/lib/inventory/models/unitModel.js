'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './unitPropertyModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/unit';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'travelerId',
    relatedModel: 'TravelerIdModel',
    includeInJSON: ['id'],
    reverseRelation: {
      type: BackboneRelational.HasOne,
      key: 'unit',
      includeInJSON: ['id', 'serial', 'unitType', 'properties'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'salesItem',
    relatedModel: 'SalesItemModel',
    includeInJSON: ['id'],
    reverseRelation: {
      type: BackboneRelational.HasOne,
      key: 'unit',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'unitType',
    relatedModel: 'UnitTypeModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasMany,
    key: 'properties',
    relatedModel: 'UnitPropertyModel',
    includeInJSON:  ['id', 'unitTypeProperty', 'integerValue', 'floatValue', 'booleanValue', 'stringValue'],
    reverseRelation: {
      type: BackboneRelational.HasOne,
      key: 'unit',
      includeInJSON: ['id'],
    }
  },{
    type: BackboneRelational.HasOne,
    key: 'organization',
    relatedModel: 'OrganizationModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    serial: null,
    description: null,
    travelerId: null,
    salesItem: null,
    properties: null,
    unitType: null,
    organization: null,
    isVoid: null,
  }
});

globalNamespace.Models.UnitModel = Model;

export default Model;