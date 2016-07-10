'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import EntityIndexView from 'lib/common/views/entity/indexView.js';

import AdminIndexView from './views/adminIndexView.js';
import AdminUsersLayoutView from './views/adminUsersLayoutView.js';

import UserCollection from 'lib/common/models/userCollection.js';
import AdminUsersEditView from './views/adminUsersEditView.js';
import adminUsersListTableLayoutTpl from './views/adminUsersListTableLayoutTpl.hbs!';
import adminUsersRowTpl from './views/adminUsersRowTpl.hbs!';

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
    'admin/menu_item/:id': 'menuLinks',
  },
  index(){
    this.users();
  },
  users(id){
    let adminIndexView =  new AdminIndexView();
    let userCollection = Radio.channel('data').request('collection', UserCollection);
    let adminUsersLayoutView = new AdminUsersLayoutView();

     var entityViewOptions = {
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

    var entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminIndexView);

    userCollection.fetch();
  },
  menuLinks(){

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