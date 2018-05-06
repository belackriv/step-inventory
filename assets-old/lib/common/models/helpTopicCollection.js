'use strict';

import BaseUrlBaseCollection from './baseUrlBaseCollection.js';

import Model from './helpTopicModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/help_topic';
  },
});