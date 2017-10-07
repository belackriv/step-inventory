'use strict';

import globalNamespace from 'lib/globalNamespace.js';

import AccountPlanChangeModel from './accountPlanChangeModel.js';

let Model = AccountPlanChangeModel.extend({
	defaults: {
		isAuto: true
	}
});

globalNamespace.Models.AccountAutoPlanChangeModel = Model;

export default Model;