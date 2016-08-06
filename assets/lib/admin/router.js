'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

//for jspm for now
import './controller.js';

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
    'user': 'users',
    'user/:id': 'users',
    'admin/user/:id': 'users',
    'menu_item': 'menuItems',
    'menu_item/:id': 'menuItems',
    'admin/menu_item/:id': 'menuItems',
    'admin_inventory': 'parts',
    'admin_inventory/part': 'parts',
    'admin_inventory/part/:id': 'parts',
    'part': 'parts',
    'part/:id': 'parts',
    'admin_inventory/part_category': 'partCategories',
    'admin_inventory/part_category/:id': 'partCategories',
    'part_category': 'partCategories',
    'part_category/:id': 'partCategories',
    'admin_inventory/part_group': 'partGroups',
    'admin_inventory/part_group/:id': 'partGroups',
    'part_group': 'partGroups',
    'part_group/:id': 'partGroups',
    'admin_inventory/bin_type': 'binTypes',
    'admin_inventory/bin_type/:id': 'binTypes',
    'bin_type': 'binTypes',
    'bin_type/:id': 'binTypes',
    'admin_inventory/bin': 'bins',
    'admin_inventory/bin/:id': 'bins',
    'bin': 'bins',
    'bin/:id': 'bins',
    'admin_inventory/inventory_movement_rule': 'inventoryMovementRules',
    'admin_inventory/inventory_movement_rule/:id': 'inventoryMovementRules',
    'inventory_movement_rule': 'inventoryMovementRules',
    'inventory_movement_rule/:id': 'inventoryMovementRules',
    'admin': 'index',
  },
  index(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.users();
    });
  },
  users(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.users(id);
    });
  },
  menuItems(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.menuItems(id);
    });
  },
  parts(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.parts(id);
    });
  },
  partCategories(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.partCategories(id);
    });
  },
  partGroups(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.partGroups(id);
    });
  },
  binTypes(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.binTypes(id);
    });
  },
  bins(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.bins(id);
    });
  },
  inventoryMovementRules(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryMovementRules(id);
    });
  },
});