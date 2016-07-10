'use strict';

import globalNamespace from 'lib/globalNamespace';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/office';
  },
  defaults: {
    name: null,
    departments: null,
  }
});

globalNamespace.Models.OfficeModel = Model;

export default Model;