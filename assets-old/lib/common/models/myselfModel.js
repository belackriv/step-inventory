'use strict';


import globalNamespace from 'lib/globalNamespace.js';
import Radio from 'backbone.radio';
import UserModel from './userModel.js';

let Model = UserModel.extend({
  initialize(){

  },
  urlRoot(){
    return this.baseUrl+'/myself';
  },
  updateCurrentTime(){
    this.set('currentTime', new Date());
  },
});

globalNamespace.Models.MyselfModel = Model;

export default Model;