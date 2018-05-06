'use strict';

import Backbone from 'backbone';
import Relational from 'backbone.relational';

var namespace =  {
  Models: {},
  Views: {}
};

Backbone.Relational = Relational;
Backbone.Relational.store.addModelScope(namespace.Models);

export default namespace;