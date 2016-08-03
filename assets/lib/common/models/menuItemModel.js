'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

import './departmentModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'route:changed', this.setUiIsActiveFromRoute);
    this.setUiIsActiveFromRoute(Backbone.history.fragment);
  },
  urlRoot(){
    return this.baseUrl+'/menu_item';
  },
  relations: [{
    type: Backbone.HasOne,
    key: 'department',
    relatedModel: 'DepartmentModel',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'menuItems',
      includeInJSON: ['id'],
    }
  },{
    type: Backbone.HasOne,
    key: 'menuLink',
    includeInJSON: ['id'],
  },{
    type: Backbone.HasMany,
    key: 'children',
    includeInJSON: ['id'],
    reverseRelation:{
      key: 'parent',
      includeInJSON: ['id'],
    }
  }],
  defaults:{
    isActive: null,
    position: null,
    menuLink: null,
    department: null,
    children: null,
    parent: null,
    uiIsActive: false,
  },
  hasChild(child){
    return this.get('children').contains(child);
  },
  setUiIsActiveFromRoute(route){
    route = (route[0]=='/')?route.slice(1):route;
    let uiIsActive = false;
    if(this.get('menuLink') && this.get('menuLink').get('url')){
      let url = this.get('menuLink').get('url');
      if(this.doesRouteMatchUrl(route, url)){
        uiIsActive = true;
        //console.log('Url Match Found:'+route+' = '+url);
      }
      _.each(this.get('menuLink').get('routeMatches'), (subUrl)=>{
        if(this.doesRouteMatchUrl(route, subUrl)){
          uiIsActive = true;
          //console.log('SubUrl Match Found:'+route+' = '+subUrl);
        }
      });
    }
    this.set('uiIsActive', uiIsActive);
    return uiIsActive;
  },
  doesRouteMatchUrl(route, url){
    url = (url[0]=='/')?url.slice(1):url;
    let re = new RegExp( '^'+url+'$');
    if(re.test(route)){
      return true;
    }
    re = new RegExp( '^'+url+'/');
    if(re.test(route)){
      return true;
    }
  },
});



globalNamespace.Models.MenuItemModel = Model;

export default Model;