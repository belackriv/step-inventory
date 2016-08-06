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
    'bin_part_count': 'binPartCounts',
    'bin_part_count/:id': 'binPartCounts',
    'inventory_part_adjustment': 'inventoryPartAdjustments',
    'inventory_part_movement': 'inventoryPartMovements',
    'inventory_audit': 'inventoryAudit',
    'inventory_audit/:id': 'inventoryAudit',
    'inventory': 'inventory'
  },
  inventory(){
    this.binPartCounts();
  },
  binPartCounts(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.binPartCounts(id);
    });
  },
  inventoryPartAdjustments(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryPartAdjustments();
    });
  },
  inventoryPartMovements(){
    Radio.channel('app').trigger('request:started');
    System.import('lib/inventory/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.inventoryPartMovements();
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