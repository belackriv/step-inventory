"use strict";

import Marionette from 'marionette';


export default Marionette.Behavior.extend({
  // you can set default options
  // just like you can in your Backbone Models
  // they will be overriden if you pass in an option with the same key
  defaults: {
    "message": "you are destroying!"
  },

  // behaviors have events that are bound to the views DOM
  events: {
    "click @ui.destroy": "warnBeforeDestroy"
  },

  warnBeforeDestroy: function() {
    alert(this.options.message);
    // every Behavior has a hook into the
    // view that it is attached to
    this.view.destroy();
  }
});
