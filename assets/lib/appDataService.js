'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import MyselfModel from 'lib/common/models/myselfModel.js';

export default Marionette.Object.extend({
  initialize(options){
    this.collections = [];
    Radio.channel('data').reply('collection', this.getCollection.bind(this));
    this.setupMyself();
  },
  getCollection(Constructor, options){
    options = _.extend({}, options);
    let collection = _.find(this.collections, (collection)=>{
     return (collection instanceof Constructor);
    });
    if(!collection){
      collection = new Constructor();
      this.collections.push(collection);
      if(options.doFetch != false){
        collection.fetch();
      }
    }
    return collection;
  },
  setupMyself(){
    this.myself = new MyselfModel();
    this.myself.fetch();
    Radio.channel('data').reply('myself', this.getMyself.bind(this));
  },
  getMyself(){
    return this.myself;
  }
});