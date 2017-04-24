'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/unit_property';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'unitTypeProperty',
    relatedModel: 'UnitTypePropertyModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    integerValue: null,
    floatValue: null,
    booleanValue: null,
    stringValue: null,
    unit: null,
    unitTypeProperty: null,
  }
});

globalNamespace.Models.UnitPropertyModel = Model;

export default Model;