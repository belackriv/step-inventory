'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/part_category';
  },
  defaults: {
    name: null,
    isActive: null
  }
});

globalNamespace.Models.PartCategoryModel = Model;

export default Model;