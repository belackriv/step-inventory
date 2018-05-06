'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/help_topic';
  },
  defaults: {
    name: null,
    heading: null,
    content: null,
  },
});

globalNamespace.Models.HelpTopicModel = Model;

export default Model;