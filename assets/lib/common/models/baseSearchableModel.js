'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';

export default Backbone.Model.extend({
  url(){
    return 'url';
  },
  getValueFromPath(path){
    var pathArray = path.split('.');
    var currentModel = this;
    for(let pathPart of pathArray){
      currentModel = currentModel.get(pathPart);
      if(typeof currentModel === 'undefined'){
        return null;
      }
      if(!Backbone.Model || !(currentModel instanceof Backbone.Model) ){
        break;
      }
    }
    return currentModel;
  },
});