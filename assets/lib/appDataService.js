'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

export default Marionette.Object.extend({
  initialize(options){
    this.collections = [];
    Radio.channel('data').reply('collection', this.getCollection.bind(this));
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
  }
});