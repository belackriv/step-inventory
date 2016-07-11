'use strict';

import globalNamespace from 'lib/globalNamespace';
import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from './baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'route:changed', this.setIsActiveFromRoute);
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
  },
  hasChild(child){
    return this.get('children').contains(child);
  },
  setIsActiveFromRoute(route){
    route = (route[0]=='/')?route.slice(1):route;
    let isActive = false;
    this.get('children').each((menuItem)=>{
      if(menuItem.setIsActiveFromRoute(route)){
        isActive = true;
      }
    });
    _.each(this.getChildRoutes(route), (childRoute)=>{
      let re = new RegExp( '^'+childRoute);
      if(re.test(route)){
        isActive = true;
      }
    });
    if(this.get('menuLink') && this.get('menuLink').get('url')){
      let url = this.get('menuLink').get('url');
      url = (url[0]=='/')?url.slice(1):url;
      let re = new RegExp( '^'+url);
      if(re.test(route)){
        isActive = true;
      }
    }
    this.set('isActive', isActive);
    return isActive;
  },
  getChildRoutes(){
    let childRoutes = [];
    if(this.get('menuLink')){
      let childRouteArray = this.childRoutes[this.get('menuLink').get('name')];
      if(childRouteArray){
        childRoutes = childRoutes.concat(childRouteArray);
      }
    }
    return childRoutes;
  },
  childRoutes: {
    'Admin Options': ['user', 'menu_item']
  }
});



globalNamespace.Models.MenuItemModel = Model;

export default Model;