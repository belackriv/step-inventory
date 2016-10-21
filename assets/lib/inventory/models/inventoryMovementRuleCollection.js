'use strict';

import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

import Model from './inventoryMovementRuleModel.js';

export default BaseUrlBaseCollection.extend({
  model: Model,
  url(){
    return this.baseUrl+'/inventory_movement_rule';
  },
});