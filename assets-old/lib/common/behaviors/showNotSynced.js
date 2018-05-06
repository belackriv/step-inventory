'use strict';

import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  ui: {
    'syncStatusIndicator': '.not-synced-alert'
  },
  modelEvents: {
    'change:isSynced': 'renderNotSyncedIndicator'
  },
  onRender(){
    this.renderNotSyncedIndicator();
  },
  renderNotSyncedIndicator(){
    if(this.view.model.get('isSynced')){
      this.ui.syncStatusIndicator.hide();
    }else{
      this.ui.syncStatusIndicator.show();
    }
  }
});