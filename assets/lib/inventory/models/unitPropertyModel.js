'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';
import UnitTypePropertyModel from 'lib/inventory/models/unitTypePropertyModel.js';

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
  },
  typeAndSet(valueName, value){
    let typedValue = null;
    if(this.get('unitTypeProperty').get('propertyType') === UnitTypePropertyModel.prototype.TYPE_INTEGER){
      typedValue = parseInt(value);
    }else if(this.get('unitTypeProperty').get('propertyType') === UnitTypePropertyModel.prototype.TYPE_FLOAT){
      typedValue = parseFloat(value);
    }else if(this.get('unitTypeProperty').get('propertyType') === UnitTypePropertyModel.prototype.TYPE_BOOLEAN){
      typedValue = value=='1'?true:false;
    }else{
      typedValue = value+'';
    }
    this.set(valueName, typedValue);
  }
});

globalNamespace.Models.UnitPropertyModel = Model;

export default Model;