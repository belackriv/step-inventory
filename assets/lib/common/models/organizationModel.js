'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/organization';
  },
  defaults: {
    name: null,
    logo: null,
  },
});

globalNamespace.Models.OrganizationModel = Model;

export default Model;