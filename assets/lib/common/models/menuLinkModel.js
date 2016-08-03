'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/menu_link';
  },
  defaults:{
    name: null,
    url: null,
    routeMatches: null,
  },
});



globalNamespace.Models.MenuItemModel = Model;

export default Model;