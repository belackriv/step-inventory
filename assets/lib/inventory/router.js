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
    'tid': 'travelerIds',
    'tid/:id': 'travelerIds',
    'show/inbound_order/:id': 'showInboundOrder',
    'show/outbound_order/:id': 'showOutboundOrder',
    'show/bin/:id': 'showBin',
    'show/tid/:id': 'showTid',
    'bin_sku_count': 'binSkuCounts',
    'bin_sku_count/:id': 'binSkuCounts',
    'sales_item': 'salesItems',
    'sales_item/:id': 'salesItems',
    'show/sales_item/:id': 'showSalesItem',
    'inventory_tid_edit': 'inventoryTravelerIdEdits',
    'inventory_tid_movement': 'inventoryTravelerIdMovements',
    'inventory_tid_transform': 'inventoryTravelerIdTransforms',
    'inventory_sales_item_edit': 'inventorySalesItemEdits',
    'inventory_sales_item_movement': 'inventorySalesItemMovements',
    'inventory_sku_adjustment': 'inventorySkuAdjustments',
    'inventory_sku_movement': 'inventorySkuMovements',
    'inventory_sku_transform': 'inventorySkuTransforms',
    'inventory_audit': 'inventoryAudit',
    'inventory_audit/:id': 'inventoryAudit',
    'inventory_action': 'inventoryAction',
    'inventory_log': 'inventoryLog'
  },
  inventoryAction(){
    this.travelerIds();
  },
  inventoryLog(){
    this.inventoryTravelerIdEdits();
  },
  travelerIds(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.travelerIds(id);
    });
  },
  showInboundOrder(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.showInboundOrder(id);
    });
  },
  showOutboundOrder(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.showOutboundOrder(id);
    });
  },
  showBin(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.showBin(id);
    });
  },
  showTid(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.showTid(id);
    });
  },
  binSkuCounts(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.binSkuCounts(id);
    });
  },
  salesItems(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.salesItems(id);
    });
  },
  showSalesItem(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.showSalesItem(id);
    });
  },
  inventoryTravelerIdEdits(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryTravelerIdEdits();
    });
  },
  inventoryTravelerIdMovements(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryTravelerIdMovements();
    });
  },
  inventoryTravelerIdTransforms(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryTravelerIdTransforms();
    });
  },
  inventorySalesItemEdits(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventorySalesItemEdits();
    });
  },
  inventorySalesItemMovements(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventorySalesItemMovements();
    });
  },
  inventorySkuAdjustments(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventorySkuAdjustments();
    });
  },
  inventorySkuMovements(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventorySkuMovements();
    });
  },
  inventorySkuTransforms(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventorySkuTransforms();
    });
  },
  inventoryAudit(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryAudit(id);
    });
  }
});