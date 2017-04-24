'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/unit_type_property_valid_value';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'unitTypeProperty',
    relatedModel: 'UnitTypePropertyModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'validValues',
      includeInJSON: ['id', 'integerValue', 'floatValue', 'booleanValue', 'stringValue'],
    }
  }],
  defaults: {
    integerValue: null,
    floatValue: null,
    booleanValue: null,
    stringValue: null,
    unitTypeProperty: null,
  }
});

globalNamespace.Models.UnitTypePropertyValidValueModel = Model;

export default Model;