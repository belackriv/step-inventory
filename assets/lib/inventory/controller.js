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

import InventorySkuAdjustmentCollection from './models/inventorySkuAdjustmentCollection.js';
import inventorySkuAdjustmentListTableLayoutTpl from './views/inventorySkuAdjustmentListTableLayoutTpl.hbs!';
import inventorySkuAdjustmentRowTpl from './views/inventorySkuAdjustmentRowTpl.hbs!';

import InventorySkuMovementCollection from './models/inventorySkuMovementCollection.js';
import inventorySkuMovementListTableLayoutTpl from './views/inventorySkuMovementListTableLayoutTpl.hbs!';
import inventorySkuMovementRowTpl from './views/inventorySkuMovementRowTpl.hbs!';

import InventorySkuTransformCollection from './models/inventorySkuTransformCollection.js';
import inventorySkuTransformListTableLayoutTpl from './views/inventorySkuTransformListTableLayoutTpl.hbs!';
import inventorySkuTransformRowTpl from './views/inventorySkuTransformRowTpl.hbs!';

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
  },
  inventoryTravelerIdTransforms(){
    let inventoryIndexView =  new InventoryLogIndexView();

    let inventoryTravelerIdTransformCollection = Radio.channel('data').request('collection', InventoryTravelerIdTransformCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryTravelerIdTransformCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromTravelerId.label', 'toTravelerId.label', 'toSalesItem.id'],
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