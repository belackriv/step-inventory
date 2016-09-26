'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import MyselfModel from 'lib/common/models/myselfModel.js';
import UserModel from 'lib/common/models/userModel.js';

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
    let doFetch = false;
    let fetchOptions = {};
    fetchOptions.data = {};
    if(options.fetchAll){
      doFetch = true;
      fetchOptions.data.disable_pagination = true;
    }
    if(!collection){
      let collectionOptions = _.extend({}, options.collectionOptions);
      collection = new Constructor(null, collectionOptions);
      this.collections.push(collection);
      if(options.doFetch !== false){
        doFetch = true;
      }
    }
    if(doFetch && !collection._fetchPending){
      collection._fetchPending = true;
      collection.fetch(fetchOptions).always(()=>{
        collection._fetchPending = false;
      });
    }
    return collection;
  },
  setupMyself(){
    this.myself = new MyselfModel();
    /*
    this.myself.urlRoot = function(){
      return this.baseUrl+'/myself';
    };
    this.myself.updateCurrentTime = function(){
      //this.set('currentTime', new Date());
    };
    this.listenTo(this.myself, 'change', ()=>{
      let test;
    });
    */
    this.myself.fetch();
    Radio.channel('data').reply('myself', this.getMyself.bind(this));
    setInterval(()=>{
      this.myself.fetch();
    }, 60000);
  },
  getMyself(){
    return this.myself;
  }
});