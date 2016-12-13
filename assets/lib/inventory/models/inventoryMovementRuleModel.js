'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import 'lib/common/models/userRoleModel.js';
import './binTypeModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.set('restrictions', []);
  },
  restrictionList: {

  },
  urlRoot(){
    return this.baseUrl+'/inventory_movement_rule';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'role',
    relatedModel: 'UserRoleModel',
    includeInJSON: ['id'],
  },{
    type: BackboneRelational.HasOne,
    key: 'binType',
    relatedModel: 'BinTypeModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    name: null,
    description: null,
    isActive: null,
    role: null,
    binType: null,
    restrictions: null,
  },
  hasRestriction(restriction){
    return (this.get('restrictions').indexOf(restriction) > -1);
  },
  addRestriction(restriction){
    if(!this.hasRestriction(restriction)){
      this.get('restrictions').push(restriction);
      this.trigger('change:restrictions', this, this.get('restrictions'), {});
    }
  },
  removeRestriction(restriction){
    let index = this.get('restrictions').indexOf(restriction);
    if(index > -1){
      this.get('restrictions').splice(index, 1);
      this.trigger('change:restrictions', this, this.get('restrictions'), {});
    }
  }
});

globalNamespace.Models.InventoryMovementRuleModel = Model;

export default Model;