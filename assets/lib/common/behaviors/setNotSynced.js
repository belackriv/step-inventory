'use strict';

import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  modelEvents: {
    'change': 'setNotSyncedIndicator'
  },
  setNotSyncedIndicator(model, options){
    if(options && options.stickitChange){
      this.view.model.set('isSynced', false);
    }
  },
});