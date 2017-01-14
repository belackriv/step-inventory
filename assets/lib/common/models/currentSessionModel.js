'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BackboneRelational from 'backbone.relational';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/current_session';
  },
  defaults: {
    startedAt: null,
    updatedAt: null,
    forUsername: null
  }
});

globalNamespace.Models.CurrentSessionModel = Model;

export default Model;