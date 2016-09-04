'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import InventoryIndexView from './views/inventoryIndexView.js';
import TravelerIdView from './views/travelerIdView.js';
import BinPartCountView from './views/binPartCountView.js';

import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';

import InventoryPartAdjustmentCollection from './models/inventoryPartAdjustmentCollection.js';
import inventoryPartAdjustmentListTableLayoutTpl from './views/inventoryPartAdjustmentListTableLayoutTpl.hbs!';
import inventoryPartAdjustmentRowTpl from './views/inventoryPartAdjustmentRowTpl.hbs!';

import InventoryPartMovementCollection from './models/inventoryPartMovementCollection.js';
import inventoryPartMovementListTableLayoutTpl from './views/inventoryPartMovementListTableLayoutTpl.hbs!';
import inventoryPartMovementRowTpl from './views/inventoryPartMovementRowTpl.hbs!';

import InventoryAuditListView from './views/inventoryAuditListView.js';
import InventoryAuditView from './views/inventoryAuditView.js';
import InventoryAuditModel from './models/inventoryAuditModel.js';

export default Marionette.Object.extend({
  index(){
    this.travelerIds();
  },
  travelerIds(id){
    let inventoryIndexView =  new InventoryIndexView();
    let travelerIdView = new TravelerIdView({id:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', travelerIdView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  binPartCounts(id){
    let inventoryIndexView =  new InventoryIndexView();
    let binPartCountView = new BinPartCountView();

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', binPartCountView]]),
        viewInstance: inventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', inventoryIndexView);

  },
  inventoryPartAdjustments(){
    let inventoryIndexView =  new InventoryIndexView();

    let inventoryPartAdjustmentCollection = Radio.channel('data').request('collection', InventoryPartAdjustmentCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryPartAdjustmentCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','forBin.name','part.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryPartAdjustmentListTableLayoutTpl,
      entityRowTpl: inventoryPartAdjustmentRowTpl,
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
  inventoryPartMovements(){
    let inventoryIndexView =  new InventoryIndexView();

    let inventoryPartMovementCollection = Radio.channel('data').request('collection', InventoryPartMovementCollection, {doFetch: false});
    let listView = new SearchableListLayoutView({
      collection: inventoryPartMovementCollection,
      listLength: 20,
      searchPath: ['byUser.firstName','byUser.lastName','fromBin.name','toBin.name','part.name'],
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: inventoryPartMovementListTableLayoutTpl,
      entityRowTpl: inventoryPartMovementRowTpl,
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