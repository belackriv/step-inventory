'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import EntityIndexView from 'lib/common/views/entity/indexView.js';

import AdminIndexView from './views/adminIndexView.js';

import UserCollection from 'lib/common/models/userCollection.js';
import AdminUsersEditView from './views/adminUsersEditView.js';
import adminUsersListTableLayoutTpl from './views/adminUsersListTableLayoutTpl.hbs!';
import adminUsersRowTpl from './views/adminUsersRowTpl.hbs!';

import MenuItemCollection from 'lib/common/models/menuItemCollection.js';
import AdminMenuItemsEditView from './views/adminMenuItemsEditView.js';
import adminMenuItemsListTableLayoutTpl from './views/adminMenuItemsListTableLayoutTpl.hbs!';
import adminMenuItemsRowTpl from './views/adminMenuItemsRowTpl.hbs!';

export default Marionette.Object.extend({
  initialize(options){
    this.options = {

    };
    Object.assign(this.options, options);
    if(!(this.options.appRouter instanceof Marionette.AppRouter)){
      throw 'Controller Must be passed an AppRouter instance.';
    }else{
      this.appRouter = this.options.appRouter;
    }
    this.appRouter.processAppRoutes(this, this.routes);
  },
  routes:  {
    'admin(/)': 'index',
    'user': 'users',
    'user/:id': 'users',
    'admin/user/:id': 'users',
    'menu_item': 'menuItems',
    'menu_item/:id': 'menuItems',
    'admin/menu_item/:id': 'menuItems',
  },
  index(){
    this.users();
  },
  users(id){
    let adminIndexView =  new AdminIndexView();
    let userCollection = Radio.channel('data').request('collection', UserCollection);
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: userCollection,
      searchPath: ['username'],
      EditView: AdminUsersEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminUsersListTableLayoutTpl,
      entityRowTpl: adminUsersRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminIndexView);

    userCollection.fetch();
  },
  menuItems(id){
    let adminIndexView =  new AdminIndexView();
    let menuItemCollection = Radio.channel('data').request('collection', MenuItemCollection);
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: menuItemCollection,
      searchPath: ['id', 'menuLink.name'],
      EditView: AdminMenuItemsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminMenuItemsListTableLayoutTpl,
      entityRowTpl: adminMenuItemsRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminIndexView);

    menuItemCollection.fetch();
  },
  buildViewStack(stack){
    for(let viewObj of stack){
      for(let [regionName, viewInstance] of viewObj.regionViewMap){
        viewObj.viewInstance.once('render', ()=>{
          viewObj.viewInstance.showChildView(regionName, viewInstance);
        });
      }
    }
  }
});