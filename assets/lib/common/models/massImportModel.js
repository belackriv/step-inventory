'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseSearchableCollection from './baseSearchableCollection.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';


let Model = BaseUrlBaseModel.extend({
  initialize(attrs, options){
    this.set('items', new BaseSearchableCollection([], {mode: 'client'}));
  },
  urlRoot(){
    return this.baseUrl+'/mass_import';
  },
  defaults: {
    items: null,
    type: null,
  },
});

globalNamespace.Models.MassImportModel = Model;

export default Model;