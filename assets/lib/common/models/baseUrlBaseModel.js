'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';

const proto = Backbone.RelationalModel.prototype;

export default Backbone.RelationalModel.extend({
  baseUrl: '/~belac/stepthrough/app_dev.php',
  fetch(options){
    Radio.channel('app').trigger('request:started');
    proto.fetch.call(this, options).always(()=>{
      Radio.channel('app').trigger('request:finished');
    });
  },
  save(options){
    Radio.channel('app').trigger('request:started');
    proto.save.call(this, options).always(()=>{
      Radio.channel('app').trigger('request:finished');
    });
  },
  getValueFromPath(path){
    var pathArray = path.split('.');
    var currentModel = this;
    for(let pathPart of pathArray){
      currentModel = currentModel.get(pathPart);
      if(typeof currentModel === 'undefined'){
        throw 'Path part "'+pathPart+'" undefined!';
      }
      if(!Backbone.RelationalModel || !(currentModel instanceof Backbone.RelationalModel) ){
        break;
      }
    }
    return currentModel;
  },
});