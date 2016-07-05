'use strict';

import Marionette from 'marionette';
import viewTpl from './noChildrenRowView.hbs!';

 export default Marionette.View.extend({
  serializeData: function(){
    return {
      colspan: this.options.colspan,
    };
  },
  template: viewTpl,
  tagName: 'tr',
});