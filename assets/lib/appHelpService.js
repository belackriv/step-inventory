'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import MyselfModel from 'lib/common/models/myselfModel.js';
import UserModel from 'lib/common/models/userModel.js';

export default Marionette.Object.extend({
  initialize(options){
    Radio.channel('help').reply('get', this.getHelpItem.bind(this));
  },
  getHelpItem(itemName){
    let item = this.helpItems[itemName];
    if(!item){
      item = this.helpItems.noHelpFound
      item.content += '"'+itemName+'"';
    }
    return item;
  },
  helpItems:{
    noHelpFound: {heading: 'Help Missing', content: 'No help content was found for topic '},
    parts: {heading: 'Parts', content: 'This is the help stuff <ul><li>One</li><li>Two</li><li>Three</li></ul>'}





  }
});