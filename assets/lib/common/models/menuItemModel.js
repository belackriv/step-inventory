'use strict';

import globalNamespace from 'lib/globalNamespace';
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
  setIsActiveFromRoute(route){
    let isActive = false;
    this.get('children').each((menuItem)=>{
      if(menuItem.setIsActiveFromRoute(route)){
        isActive = true;
      }
    });
    if(this.get('menuLink')){
      let re = new Regexp('^'.menuLink.url);
      if(re.test(route)){
        isActive = true;
      }
    }
    this.set('isActive', isActive);
    return isActive;
  }
});



globalNamespace.Models.MenuItemModel = Model;

export default Model;