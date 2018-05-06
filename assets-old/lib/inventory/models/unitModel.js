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
  },
  findPropertyByUnitTypePropertyId(unitTypePropertyId){
    let foundProperty = null;
    this.get('properties').each((property)=>{
      if(property.get('unitTypeProperty').id == unitTypePropertyId){
        foundProperty = property;
      }
    });
    return foundProperty;
  },
  getMassUpdateAttrs(){
    let attrs =  {
      id: this.get('id'),
      serial: this.get('serial'),
      unitType: {id: this.get('unitType').id},
      properties: []
    };
    this.get('properties').each((property)=>{
      attrs.properties.push({
        id: property.get('id'),
        unitTypeProperty: property.get('unitTypeProperty'),
        integerValue: property.get('integerValue'),
        floatValue: property.get('floatValue'),
        booleanValue: property.get('booleanValue'),
        stringValue: property.get('stringValue'),
      })
    });
    return attrs;
  }
});

globalNamespace.Models.UnitModel = Model;

export default Model;