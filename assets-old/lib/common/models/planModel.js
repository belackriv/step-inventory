'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/plan';
  },
  defaults: {
    name: null,
    description: null,
    amount: null,
    currency: null,
    interval: null,
    intervalCount: null,
    trialPeriodDays: null,
    maxConcurrentUsers: null,
    maxSkus: null,
    isActive: null,
  },
});

globalNamespace.Models.PlanModel = Model;

export default Model;