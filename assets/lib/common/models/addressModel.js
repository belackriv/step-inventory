'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/address';
  },
  defaults: {
    street: null,
    unit: null,
    city: null,
    state: null,
    postalCode: null,
    country: null,
    type: null,
    note: null,
    client: null,
    customer: null,
  }
});

globalNamespace.Models.AddressModel = Model;

export default Model;