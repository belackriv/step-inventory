'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  urlRoot(){
    return this.baseUrl+'/single_query_report_parameter';
  },
  defaults: {
		isSynced: true,
		name: null,
		title: null,
		priority: null,
		type: null,
		isFuzzy: null,
		template: null,
		singleQueryReport: null,
	}
});

globalNamespace.Models.SingleQueryReportParameterModel = Model;

export default Model;