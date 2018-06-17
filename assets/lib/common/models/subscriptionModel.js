'use strict';

import jquery from 'jquery';
import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './planModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/subscription';
  },
  relations: [{
    type: BackboneRelational.HasOne,
    key: 'account',
    relatedModel: 'AccountModel',
    includeInJSON: ['id'],
    reverseRelation: false
  },{
    type: BackboneRelational.HasOne,
    key: 'plan',
    relatedModel: 'PlanModel',
    includeInJSON: ['id'],
    reverseRelation: false
  }],
  defaults: {
    account: null,
    plan: null,
    createdAt: null,
    cancelAtPeriodEnd: null,
    canceledAt: null,
    currentPeriodEnd: null,
    currentPeriodStart: null,
    endedAt: null,
    quantity: null,
    startAt: null,
    status: null,
    taxPercent: null,
    trialEnd: null,
    trialStart: null,
  },
  cancel(){
    let thisModel = this;
    return new Promise((resolve, reject)=>{
      jquery.ajax('/subscription_cancel',{
        accepts: {
          json: 'application/json'
        },
        dataType: 'json'
      }).done((subscriptionData)=>{
        thisModel.set(subscriptionData);
        resolve(subscriptionData);
      }).fail((response)=>{
        reject(response);
      });
    });
  }
});

globalNamespace.Models.SubscriptionModel = Model;

export default Model;