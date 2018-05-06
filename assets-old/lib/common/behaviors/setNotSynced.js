'use strict';

import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  initialize(){
  	if(this.view.options.model && !this.view.options.model.has('isSynced')){
      this.view.options.model.set({isSynced: true}, {silent: true});
    }
  },
  modelEvents: {
    'change': 'setNotSyncedIndicator'
  },
  setNotSyncedIndicator(model, options){
    if(options && options.stickitChange){
      this.view.model.set('isSynced', false);
    }
  },
});