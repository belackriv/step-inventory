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
    return this.baseUrl+'/inventory_alert_log';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'inventoryAlert',
    relatedModel: 'InventoryAlertModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    inventoryAlert: null,
    performedAt: null,
    count: null,
    isActive: null,
  },
  dismissAlert(){
    let thisModel = this;
    return new Promise((resolve, reject)=>{
      jquery.ajax(thisModel.url()+'/dismiss',{
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

globalNamespace.Models.InventoryAlertLogModel = Model;

export default Model;