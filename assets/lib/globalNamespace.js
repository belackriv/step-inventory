'use strict';

import Backbone from 'backbone';

var namespace =  {
  Models: {},
  Views: {}
};

Backbone.Relational.store.addModelScope(namespace.Models);

export default namespace;