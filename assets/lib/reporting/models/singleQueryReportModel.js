'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import SingleQueryReportParameterModel from './singleQueryReportParameterModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/single_query_report';
  },
  relations: [{
	    type: Backbone.HasMany,
	    key: 'singleQueryReportParameters',
	    relatedModel: 'SingleQueryReportParameterModel',
	    includeInJSON: ['id'],
	    reverseRelation: {
	      key: 'singleQueryReport',
	      includeInJSON: false
	    }
	}],
	defaults: {
		isSynced: true,
		createdAt: null,
		updatedAt: null,
		tag: null,
		name: null,
		filename: null,
		isEnabled: null,
		columns: null,
		parameterWhiteList: null,
		singleQueryReportParameters: null,
		singleQueryReportRoles: null
	}
});

globalNamespace.Models.SingleQueryReportModel = Model;

export default Model;