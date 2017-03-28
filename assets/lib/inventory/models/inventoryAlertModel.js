'use strict';

import jquery from 'jquery';
import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userRoleModel.js';
import './binTypeModel.js';

const TYPE_LESS_THAN = 1;
const TYPE_GREATER_THAN = 2;

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/inventory_alert';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'department',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'sku',
    relatedModel: 'SkuModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    department: null,
    sku: null,
    isActive: null,
    count: null,
    type: null,
    types: {
      [TYPE_LESS_THAN]: 'Less Than',
      [TYPE_GREATER_THAN]: 'Greater Than'
    }
  },
  runAlert(){
    let thisModel = this;
    return new Promise((resolve, reject)=>{
      jquery.ajax(thisModel.url()+'/run',{
        accepts: {
          json: 'application/json'
        },
        dataType: 'json'
      }).done((resultsData)=>{
        resolve(resultsData);
      }).fail((response)=>{
        reject(response);
      });
    });
  }
});

globalNamespace.Models.InventoryAlertModel = Model;

export default Model;