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
    'organization': 'organizations',
    'organization/:id': 'organizations',
    'admin/organization/:id': 'organizations',
    'user': 'users',
    'user/:id': 'users',
    'admin/user/:id': 'users',
    'office': 'offices',
    'office/:id': 'offices',
    'admin/office/:id': 'offices',
    'department': 'departments',
    'department/:id': 'departments',
    'admin/department/:id': 'departments',

    'announcement': 'announcements',
    'announcement/:id': 'announcements',
    'admin/announcement/:id': 'announcements',

    'menu_item': 'menuItems',
    'menu_item/:id': 'menuItems',
    'admin/menu_item/:id': 'menuItems',
    'menu_link': 'menuLinks',
    'menu_link/:id': 'menuLinks',
    'admin/menu_link/:id': 'menuLinks',
    'help_topic': 'helpTopics',
    'help_topic/:id': 'helpTopics',
    'admin/help_topic/:id': 'helpTopics',

    'admin_inventory': 'skus',
    'admin_inventory/sku': 'skus',
    'admin_inventory/sku/:id': 'skus',
    'sku': 'skus',
    'sku/:id': 'skus',
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
    'admin_inventory/commodity': 'commodities',
    'admin_inventory/commodity/:id': 'commodities',
    'commodity': 'commodities',
    'commodity/:id': 'commodities',
    'admin_inventory/unit_type': 'unitTypes',
    'admin_inventory/unit_type/:id': 'unitTypes',
    'unit_type': 'unitTypes',
    'unit_type/:id': 'unitTypes',
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
    'admin_accounting': 'clients',
    'client': 'clients',
    'client/:id': 'clients',
    'customer': 'customers',
    'customer/:id': 'customers',
    'inbound_order': 'inboundOrders',
    'inbound_order/:id': 'inboundOrders',
    'outbound_order': 'outboundOrders',
    'outbound_order/:id': 'outboundOrders',
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
  organizations(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.organizations(id);
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
  offices(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.offices(id);
    });
  },
  departments(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.departments(id);
    });
  },
  announcements(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.announcements(id);
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
  menuLinks(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.menuLinks(id);
    });
  },
  helpTopics(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.helpTopics(id);
    });
  },
  skus(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.skus(id);
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
  commodities(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.commodities(id);
    });
  },
  unitTypes(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.unitTypes(id);
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
  clients(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.clients(id);
    });
  },
  customers(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.customers(id);
    });
  },
  inboundOrders(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inboundOrders(id);
    });
  },
  outboundOrders(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/admin/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.outboundOrders(id);
    });
  },
});