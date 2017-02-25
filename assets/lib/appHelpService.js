'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import HelpTopicCollection from 'lib/common/models/helpTopicCollection.js';


export default Marionette.Object.extend({
  initialize(options){
    Radio.channel('help').reply('get', this.getHelpItem.bind(this));
    this.helpItems = new HelpTopicCollection([{name: 'noHelpFound', heading: 'Help Missing', content: 'No help content was found for topic '}]);
  },
  getHelpItem(itemName){
    return new Promise((resolve, reject)=>{
      let item = this.helpItems.find({name:itemName});
      if(!item){
        let collection = new HelpTopicCollection();
        collection.fetch({data: {terms: itemName, search: 'name'}}).then(()=>{
          this.helpItems.add(collection.models);
          item = this.helpItems.find({name:itemName});
          if(!item){
            item = this.helpItems.find({name:'noHelpFound'});
          }
          resolve(item);
        });
      }else{
        resolve(item);
      }
    });
  },
  helpItems: null
});