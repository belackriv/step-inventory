'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import './partModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/bin_part_count';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'part',
    relatedModel: 'PartModel',
    includeInJSON: ['id'],
  }],
  defaults: {
    bin: null,
    part: null,
    count: null
  },
});

globalNamespace.Models.BinPartCountModel = Model;

export default Model;