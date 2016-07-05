//credit to https://github.com/bazineta for the original

'use strict';

import * as _ from 'underscore';
import * as Marionette from 'marionette';

// Save original Backbone.Stickit calls.
var stickit = Marionette.View.prototype.stickit;
var addBinding = Marionette.View.prototype.addBinding;
var unstickit = Marionette.View.prototype.unstickit;

// Stickit selectors can be hashes, strings, or undefined.  We need to
// normalize each type against the provided ui hash, which itself might be undefined.
var normalizeUIString = function normalizeUIString(uiString, ui) {
  return uiString.replace(/@ui\.[a-zA-Z_$0-9]*/g, function (r) {
    return ui[r.slice(4)];
  });
};

var normalizeSelector = function(selector, ui) {
  switch (false) {
    case !_.isObject(selector):
      return Marionette.View.prototype.normalizeUIKeys(selector, ui);
    case !_.isString(selector):
      return normalizeUIString(selector, ui);
    default:
      return selector;
  }
};

//Return UI bindings for the provided view.
var uiBindings = function(view) {
  return _.result(view, '_uiBindings') || _.result(view, 'ui');
};

// Shim the three standard Stickit calls into Marionette View objects so
// that we can use the @ui syntax for bindings; this eliminates repetition of
// possibly long and complex selectors and allows us to have one definition
// for them, in the ui hash or function for the View.
_.extend(Marionette.View.prototype, {
  stickit: function(optionalModel, optionalBindings) {
    return stickit.call(this, optionalModel, this.normalizeUIKeys(optionalBindings || _.result(this, 'bindings') || {}));
  },
  addBinding: function(optionalModel, selector, configuration) {
    return addBinding.call(this, optionalModel, normalizeSelector(selector, uiBindings(this)), configuration);
  },
  unstickit: function(optionalModel, optionalSelector) {
    return unstickit.call(this, optionalModel, normalizeSelector(optionalSelector, uiBindings(this)));
  }
});
