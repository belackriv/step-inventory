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
  isGrantedRole(role, userAccount, subRole){
    var user = userAccount?userAccount:this;
    if(user.get('userRoles') && user.get('userRoles') instanceof Backbone.Collection){
	    return user.get('userRoles').some((userRole)=>{
	    	if( userRole.get('role').get('role') == role){
		      return true;
		    }
		    var roleLookup = subRole?subRole:userRole.get('role').get('role');
		    var userGrantedRoles = this.get('roleHierarchy')[roleLookup];
		    if(userGrantedRoles){
		      if(userGrantedRoles.indexOf(role) > -1){
		        return true;
		      }else{
		        for(let subRole of userGrantedRoles){
		          if(this.isGrantedRole(role, user, subRole)){
		            return true;
		          }
		        }
		      }
		    }
	    });
	}else{
		return false;
	}
  }
});

globalNamespace.Models.MyselfModel = Model;

export default Model;