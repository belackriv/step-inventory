'use strict';


import globalNamespace from 'lib/globalNamespace.js';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import UserModel from './userModel.js';

let Model = UserModel.extend({
  urlRoot(){
    return this.baseUrl+'/myself';
  },
  updateCurrentTime(){
    this.set('currentTime', new Date());
  },
});

globalNamespace.Models.MyselfModel = Model;

export default Model;