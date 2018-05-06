'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/part_group';
  },
  defaults: {
    name: null,
    isActive: null
  }
});

globalNamespace.Models.PartGroupModel = Model;

export default Model;