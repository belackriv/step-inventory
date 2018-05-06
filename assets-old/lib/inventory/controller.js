'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import InventoryActionIndexView from './views/inventoryActionIndexView.js';
import InventoryLogIndexView from './views/inventoryLogIndexView.js';

import TravelerIdView from './views/travelerIdView.js';
import BinSkuCountView from './views/binSkuCountView.js';
import SalesItemView from './views/salesItemView.js';

import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import InventoryTravelerIdEditCollection from './models/inventoryTravelerIdEditCollection.js';
import inventoryTravelerIdEditListTableLayoutTpl from './views/inventoryTravelerIdEditListTableLayoutTpl.hbs!';
import inventoryTravelerIdEditRowTpl from './views/inventoryTravelerIdEditRowTpl.hbs!';

import InventoryTravelerIdMovementCollection from './models/inventoryTravelerIdMovementCollection.js';
import inventoryTravelerIdMovementListTableLayoutTpl from './views/inventoryTravelerIdMovementListTableLayoutTpl.hbs!';
import inventoryTravelerIdMovementRowTpl from './views/inventoryTravelerIdMovementRowTpl.hbs!';

import InventoryTravelerIdTransformCollection from './models/inventoryTravelerIdTransformCollection.js';
import inventoryTravelerIdTransformListTableLayoutTpl from './views/inventoryTravelerIdTransformListTableLayoutTpl.hbs!';
import inventoryTravelerIdTransformRowTpl from './views/inventoryTravelerIdTransformRowTpl.hbs!';

import InventorySalesItemEditCollection from './models/inventorySalesItemEditCollection.js';
import inventorySalesItemEditListTableLayoutTpl from './views/inventorySalesItemEditListTableLayoutTpl.hbs!';
import inventorySalesItemEditRowTpl from './views/inventorySalesItemEditRowTpl.hbs!';

import InventorySalesItemMovementCollection from './models/inventorySalesItemMovementCollection.js';
import inventorySalesItemMovementListTableLayoutTpl from './views/inventorySalesItemMovementListTableLayoutTpl.hbs!';
import inventorySalesItemMovementRowTpl from './views/inventorySalesItemMovementRowTpl.hbs!';

import InventorySkuAdjustmentCollection from './models/inventorySkuAdjustmentCollection.js';
import inventorySkuAdjustmentListTableLayoutTpl from './views/inventorySkuAdjustmentListTableLayoutTpl.hbs!';
import inventorySkuAdjustmentRowTpl from './views/inventorySkuAdjustmentRowTpl.hbs!';

import InventorySkuMovementCollection from './models/inventorySkuMovementCollection.js';
import inventorySkuMovementListTableLayoutTpl from './views/inventorySkuMovementListTableLayoutTpl.hbs!';
import inventorySkuMovementRowTpl from './views/inventorySkuMovementRowTpl.hbs!';

import InventorySkuTransformCollection from './models/inventorySkuTransformCollection.js';
import inventorySkuTransformListTableLayoutTpl from './views/inventorySkuTransformListTableLayoutTpl.hbs!';
import inventorySkuTransformRowTpl from './views/inventorySkuTransformRowTpl.hbs!';

import InventoryAlertLogCollection from './models/inventoryAlertLogCollection.js';
import inventoryAlertLogListTableLayoutTpl from './views/inventoryAlertLogListTableLayoutTpl.hbs!';
import inventoryAlertLogRowTpl from './views/inventoryAlertLogRowTpl.hbs!';

import InventoryAuditListView from './views/inventoryAuditListView.js';
import InventoryAuditView from './views/inventoryAuditView.js';
import InventoryAuditModel from './models/inventoryAuditModel.js';

export default Marionette.Object.extend({
  index(){
    this.travelerIds();
  },
  travelerIds(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let travelerIdView = new TravelerIdView({id:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'travelerIds');
  },
  showInboundOrder(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let travelerIdView = new TravelerIdView({inboundOrder:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  showOutboundOrder(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let travelerIdView = new SalesItemView({outboundOrder:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  showBin(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let travelerIdView = new TravelerIdView({bin:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  showTid(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let travelerIdView = new TravelerIdView({show:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  salesItems(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let salesItemView = new SalesItemView({id:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', salesItemView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'salesItems');
  },
  showSalesItem(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let salesItemView = new SalesItemView({shoq:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', salesItemView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  inventoryTravelerIdEdits(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventoryTravelerIdEditCollection = Radio.channel('data').request('collection', InventoryTravelerIdEditCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryTravelerIdEditCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','travelerId.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryTravelerIdEditListTableLayoutTpl,
      entityRowTpl: inventoryTravelerIdEditRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventoryTravelerIdEdits');
  },
  inventoryTravelerIdMovements(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventoryTravelerIdMovementCollection = Radio.channel('data').request('collection', InventoryTravelerIdMovementCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryTravelerIdMovementCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromBin.name','toBin.name','travelerId.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryTravelerIdMovementListTableLayoutTpl,
      entityRowTpl: inventoryTravelerIdMovementRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventoryTravelerIdMovements');
  },
  inventoryTravelerIdTransforms(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventoryTravelerIdTransformCollection = Radio.channel('data').request('collection', InventoryTravelerIdTransformCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryTravelerIdTransformCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromTravelerIds.label', 'toTravelerIds.label', 'toSalesItems.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryTravelerIdTransformListTableLayoutTpl,
      entityRowTpl: inventoryTravelerIdTransformRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventoryTravelerIdTransforms');
  },
  inventorySalesItemEdits(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventorySalesItemEditCollection = Radio.channel('data').request('collection', InventorySalesItemEditCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventorySalesItemEditCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','salesItem.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventorySalesItemEditListTableLayoutTpl,
      entityRowTpl: inventorySalesItemEditRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventorySalesItemEdits');
  },
  inventorySalesItemMovements(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventorySalesItemMovementCollection = Radio.channel('data').request('collection', InventorySalesItemMovementCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventorySalesItemMovementCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromBin.name','toBin.name','salesItem.label'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventorySalesItemMovementListTableLayoutTpl,
      entityRowTpl: inventorySalesItemMovementRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventorySalesItemMovements');
  },
  binSkuCounts(id){
    let inventoryIndexView =  new InventoryActionIndexView();
    let binSkuCountView = new BinSkuCountView();

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', binSkuCountView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'binSkuCounts');
  },
  inventorySkuAdjustments(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventorySkuAdjustmentCollection = Radio.channel('data').request('collection', InventorySkuAdjustmentCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventorySkuAdjustmentCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','forBin.name','sku.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventorySkuAdjustmentListTableLayoutTpl,
      entityRowTpl: inventorySkuAdjustmentRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventorySkuAdjustments');
  },
  inventorySkuMovements(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventorySkuMovementCollection = Radio.channel('data').request('collection', InventorySkuMovementCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventorySkuMovementCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromBin.name','toBin.name','sku.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventorySkuMovementListTableLayoutTpl,
      entityRowTpl: inventorySkuMovementRowTpl,
      colspan: 7,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventorySkuMovements');
  },
  inventorySkuTransforms(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventorySkuTransformCollection = Radio.channel('data').request('collection', InventorySkuTransformCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventorySkuTransformCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromBinSkuCount.sku.name','fromBinSkuCount.bin.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventorySkuTransformListTableLayoutTpl,
      entityRowTpl: inventorySkuTransformRowTpl,
      colspan: 7,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventorySkuTransforms');
  },
  inventoryAlertLogs(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventoryAlertLogCollection = Radio.channel('data').request('collection', InventoryAlertLogCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryAlertLogCollection,
      listLength: 20,
      searchPath: ['inventoryAlert.department.name', 'inventoryAlert.sku.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryAlertLogListTableLayoutTpl,
      entityRowTpl: inventoryAlertLogRowTpl,
      colspan: 6,
    });

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', listView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);
    Radio.channel('help').trigger('show', 'inventoryAlertLogs');
  },
  inventoryAudit(id){
    let view = new InventoryAuditListView();
    if(id){
      let auditModel = InventoryAuditModel.findOrCreate({id:id})
      view = new InventoryAuditView({
        model: auditModel
      });
      auditModel.fetch().done(()=>{
        Radio.channel('app').trigger('show:view', view);
      });
    }else{
      Radio.channel('app').trigger('show:view', view);
    }
    Radio.channel('help').trigger('show', 'inventoryAudit');
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