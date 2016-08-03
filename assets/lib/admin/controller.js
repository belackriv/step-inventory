'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import EntityIndexView from 'lib/common/views/entity/indexView.js';

import AdminIndexView from './views/adminIndexView.js';
import AdminInventoryIndexView from './views/adminInventoryIndexView.js';

import UserCollection from 'lib/common/models/userCollection.js';
import AdminUsersEditView from './views/adminUsersEditView.js';
import adminUsersListTableLayoutTpl from './views/adminUsersListTableLayoutTpl.hbs!';
import adminUsersRowTpl from './views/adminUsersRowTpl.hbs!';

import MenuItemCollection from 'lib/common/models/menuItemCollection.js';
import AdminMenuItemsEditView from './views/adminMenuItemsEditView.js';
import adminMenuItemsListTableLayoutTpl from './views/adminMenuItemsListTableLayoutTpl.hbs!';
import adminMenuItemsRowTpl from './views/adminMenuItemsRowTpl.hbs!';

import PartCollection from 'lib/inventory/models/partCollection.js';
import AdminPartsEditView from './views/adminPartsEditView.js';
import adminPartsListTableLayoutTpl from './views/adminPartsListTableLayoutTpl.hbs!';
import adminPartsRowTpl from './views/adminPartsRowTpl.hbs!';

import PartCategoryCollection from 'lib/inventory/models/partCategoryCollection.js';
import AdminPartCategoriesEditView from './views/adminPartCategoriesEditView.js';
import adminPartCategoriesListTableLayoutTpl from './views/adminPartCategoriesListTableLayoutTpl.hbs!';
import adminPartCategoriesRowTpl from './views/adminPartCategoriesRowTpl.hbs!';

import PartGroupCollection from 'lib/inventory/models/partGroupCollection.js';
import AdminPartGroupsEditView from './views/adminPartGroupsEditView.js';
import adminPartGroupsListTableLayoutTpl from './views/adminPartGroupsListTableLayoutTpl.hbs!';
import adminPartGroupsRowTpl from './views/adminPartGroupsRowTpl.hbs!';

import BinTypeCollection from 'lib/inventory/models/binTypeCollection.js';
import AdminBinTypesEditView from './views/adminBinTypesEditView.js';
import adminBinTypesListTableLayoutTpl from './views/adminBinTypesListTableLayoutTpl.hbs!';
import adminBinTypesRowTpl from './views/adminBinTypesRowTpl.hbs!';

import BinCollection from 'lib/inventory/models/binCollection.js';
import AdminBinsEditView from './views/adminBinsEditView.js';
import adminBinsListTableLayoutTpl from './views/adminBinsListTableLayoutTpl.hbs!';
import adminBinsRowTpl from './views/adminBinsRowTpl.hbs!';

import InventoryMovementRuleCollection from 'lib/inventory/models/inventoryMovementRuleCollection.js';
import AdminInventoryMovementRulesEditView from './views/adminInventoryMovementRulesEditView.js';
import adminInventoryMovementRulesListTableLayoutTpl from './views/adminInventoryMovementRulesListTableLayoutTpl.hbs!';
import adminInventoryMovementRulesRowTpl from './views/adminInventoryMovementRulesRowTpl.hbs!';

export default Marionette.Object.extend({
   index(){
    this.users();
  },
  users(id){
    let adminIndexView =  new AdminIndexView();
    let userCollection = Radio.channel('data').request('collection', UserCollection, {doFetch: false});
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
  },
  menuItems(id){
    let adminIndexView =  new AdminIndexView();
    let menuItemCollection = Radio.channel('data').request('collection', MenuItemCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: menuItemCollection,
      searchPath: ['id', 'menuLink.name', 'department.name'],
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
  },
  parts(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let partCollection = Radio.channel('data').request('collection', PartCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: partCollection,
      searchPath: ['name','partId','partAltId','partGroup.name','partCategory.name'],
      EditView: AdminPartsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminPartsListTableLayoutTpl,
      entityRowTpl: adminPartsRowTpl,
      colspan: 8
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
  },
  partCategories(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let partCategoryCollection = Radio.channel('data').request('collection', PartCategoryCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: partCategoryCollection,
      searchPath: ['name'],
      EditView: AdminPartCategoriesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminPartCategoriesListTableLayoutTpl,
      entityRowTpl: adminPartCategoriesRowTpl,
      colspan: 3
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
  },
  partGroups(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let partGroupCollection = Radio.channel('data').request('collection', PartGroupCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: partGroupCollection,
      searchPath: ['name'],
      EditView: AdminPartGroupsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminPartGroupsListTableLayoutTpl,
      entityRowTpl: adminPartGroupsRowTpl,
      colspan: 3
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
  },
  binTypes(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let binTypeCollection = Radio.channel('data').request('collection', BinTypeCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: binTypeCollection,
      searchPath: ['name'],
      EditView: AdminBinTypesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminBinTypesListTableLayoutTpl,
      entityRowTpl: adminBinTypesRowTpl,
      colspan: 4
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
  },
  bins(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let binCollection = Radio.channel('data').request('collection', BinCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: binCollection,
      searchPath: ['name','partCategory.name','binType.name', 'parent.name'],
      EditView: AdminBinsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminBinsListTableLayoutTpl,
      entityRowTpl: adminBinsRowTpl,
      colspan: 7
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
  },
  inventoryMovementRules(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let inventoryMovementRuleCollection = Radio.channel('data').request('collection', InventoryMovementRuleCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: inventoryMovementRuleCollection,
      searchPath: ['name','role.name','binType.name'],
      EditView: AdminInventoryMovementRulesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminInventoryMovementRulesListTableLayoutTpl,
      entityRowTpl: adminInventoryMovementRulesRowTpl,
      colspan: 7
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminInventoryIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminInventoryIndexView);
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