"use strict";

import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  defaults: {},
  modelEvents: {
    "change": "alertByOutlineFlash"
  },
  alertByOutlineFlash(event){
    var behavior = this;
    var changedPropertyNames = Object.keys(event.changed);
    var pulseOn = true;
    var pulsate = function(propName){
      if(pulseOn){
        behavior.$el.find('.stepthrough-change-outline-alert-'+propName.toLowerCase()).addClass('stepthrough-change-outline-alert-alert');
        pulseOn = false;
      }else{
        behavior.$el.find('.stepthrough-change-outline-alert-'+propName.toLowerCase()).removeClass('stepthrough-change-outline-alert-alert');
        pulseOn = true;
      }

    };
    _.each(changedPropertyNames, function(propName){
      var interval = setInterval(function(){ pulsate(propName); },200);
      setTimeout(function(){ clearInterval(interval); }, 1000);
    });

  }
});