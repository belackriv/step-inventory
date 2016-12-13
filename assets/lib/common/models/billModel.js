'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/bill';
  },
  defaults: {
    chargedAt: null,
    amount: null,
    account: null,
  },
});

globalNamespace.Models.BillModel = Model;

export default Model;