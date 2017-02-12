'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/contact';
  },
  defaults: {
    firstName: null,
    lastName: null,
    emailAddress: null,
    phoneNumber: null,
    position: null,
    client: null,
    customer: null,
  }
});

globalNamespace.Models.ContactModel = Model;

export default Model;