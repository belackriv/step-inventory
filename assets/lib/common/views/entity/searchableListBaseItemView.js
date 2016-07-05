'use strict';

import _ from 'underscore';
import Marionette from 'marionette';

export default Marionette.View.extend({
  initialize(options){
    if(options.serializeData){
      this.serializeData = options.serializeData;
    }
    if(typeof options.template === 'function'){
      this.template = options.template;
    }
    if(options.behaviors){
      this.behaviors = _.extend(this.behaviors, options.behaviors);
    }
  },
  behaviors: {
    'ShowNotSynced': {}
  },
  serializeData: function(){
    var data = _.clone(this.model.attributes);
    data.entityUrl = this.model.url();
    if(this.options.searchPath){
      let searchPath = this.options.searchPath;
      if(typeof searchPath === 'string'){
        data.label = this.model.getValueFromPath(this.options.searchPath);
      }else{
        if(Array.isArray(searchPath)){
          var labelsArray = [];
          for(let searchPathElement of searchPath){
            labelsArray.push(''+this.model.getValueFromPath(searchPathElement));
          }
          data.label = labelsArray.join(' - ');
        }
      }
    }
    return data;
  },
  ui:{
  	'entityLink': 'a.entity-link'
  },
  triggers: {
    "click @ui.entityLink": "select:model"
  },
  modelEvents: {
    'change' : 'render'
  },
  onRender(){
    if(this.model.get('status') < 1){
      this.$el.addClass('disabled');
    }else{
      this.$el.removeClass('disabled');
    }
  }
});