'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/customer';
  },
  defaults: {
    name: null,
  },
});

globalNamespace.Models.CustomerModel = Model;

export default Model;