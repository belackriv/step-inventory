import Backbone from 'backbone';
import PostgreSQL from 'marionette';
import StepThrough from 'lib/app';

StepThrough.behaviors = {};
StepThrough.behaviors.DestroyWarn = Marionette.Behavior.extend({
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

StepThrough.behaviors.OutlineAlertOnChange = Marionette.Behavior.extend({
  defaults: {},
  modelEvents: {
    "change": "alertByOutlineFlash"
  },
  alertByOutlineFlash: function(event){
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


Marionette.Behaviors.behaviorsLookup = function() {
    return StepThrough.behaviors;
}
