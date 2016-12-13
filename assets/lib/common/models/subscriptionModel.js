'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/subscription';
  },
  defaults: {
    name: null,
    description: null,
    isActive: null,
    amount: null,
    maxConcurrentUsers: null,
    maxSkus: null,
  },
});

globalNamespace.Models.SubscriptionModel = Model;

export default Model;