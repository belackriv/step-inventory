'use strict';

import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  onRender(){
    this.view.stickit();
  }
});