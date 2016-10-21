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
    'show/bin/:id': 'showBin',
    'show/tid/:id': 'showTid',
    'inventory_tid_edit': 'inventoryTravelerIdEdits',
    'inventory_tid_movement': 'inventoryTravelerIdMovements',
    'bin_sku_count': 'binSkuCounts',
    'bin_sku_count/:id': 'binSkuCounts',
    'inventory_sku_adjustment': 'inventorySkuAdjustments',
    'inventory_sku_movement': 'inventorySkuMovements',
    'inventory_audit': 'inventoryAudit',
    'inventory_audit/:id': 'inventoryAudit',
    'inventory': 'inventory'
  },
  inventory(){
    this.travelerIds();
  },
  travelerIds(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.travelerIds(id);
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
  binSkuCounts(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.binSkuCounts(id);
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
  inventoryAudit(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryAudit(id);
    });
  }
});