'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './unitTypePropertyValidValueModel.js';

const TYPE_INTEGER = 'integer';
const TYPE_FLOAT = 'float';
const TYPE_BOOLEAN = 'boolean';
const TYPE_STRING = 'string';

const types = {
  [TYPE_INTEGER] : 'Integer',
  [TYPE_STRING] : 'String',
  [TYPE_BOOLEAN] : 'Boolean',
  [TYPE_FLOAT] :'Float',
};


let Model = BaseUrlBaseModel.extend({
  TYPE_INTEGER: TYPE_INTEGER,
  TYPE_FLOAT: TYPE_FLOAT,
  TYPE_BOOLEAN: TYPE_BOOLEAN,
  TYPE_STRING: TYPE_STRING,
  types: types,
  urlRoot(){
    return this.baseUrl+'/unit_type_property';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'unitType',
    relatedModel: 'UnitTypeModel',
    includeInJSON: ['id'],
    reverseRelation: {
      key: 'properties',
      includeInJSON: false,
    }
  }],
  defaults: {
    propertyName: null,
    propertyType: null,
    isRequired: null,
    unitType: null,
    validValues: null,
  }
});

globalNamespace.Models.UnitTypePropertyModel = Model;

export default Model;