'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import EntityIndexView from 'lib/common/views/entity/indexView.js';

import AdminIndexView from './views/adminIndexView.js';
import AdminInventoryIndexView from './views/adminInventoryIndexView.js';
import AdminAccountingIndexView from './views/adminAccountingIndexView.js';

import OrganizationCollection from  'lib/common/models/organizationCollection.js';
import AdminOrganizationsEditView from  './views/adminOrganizationsEditView.js';
import adminOrganizationsListTableLayoutTpl from  './views/adminOrganizationsListTableLayoutTpl.hbs!';
import adminOrganizationsRowTpl from  './views/adminOrganizationsRowTpl.hbs!';


import UserCollection from 'lib/common/models/userCollection.js';
import AdminUsersEditView from './views/adminUsersEditView.js';
import adminUsersListTableLayoutTpl from './views/adminUsersListTableLayoutTpl.hbs!';
import adminUsersRowTpl from './views/adminUsersRowTpl.hbs!';

import OfficeCollection from  'lib/common/models/officeCollection.js';
import AdminOfficesEditView from  './views/adminOfficesEditView.js';
import adminOfficesListTableLayoutTpl from  './views/adminOfficesListTableLayoutTpl.hbs!';
import adminOfficesRowTpl from  './views/adminOfficesRowTpl.hbs!';

import DepartmentCollection from  'lib/common/models/departmentCollection.js';
import AdminDepartmentsEditView from  './views/adminDepartmentsEditView.js';
import adminDepartmentsListTableLayoutTpl from  './views/adminDepartmentsListTableLayoutTpl.hbs!';
import adminDepartmentsRowTpl from  './views/adminDepartmentsRowTpl.hbs!';

import MenuItemCollection from 'lib/common/models/menuItemCollection.js';
import AdminMenuItemsEditView from './views/adminMenuItemsEditView.js';
import adminMenuItemsListTableLayoutTpl from './views/adminMenuItemsListTableLayoutTpl.hbs!';
import adminMenuItemsRowTpl from './views/adminMenuItemsRowTpl.hbs!';

import MenuLinkCollection from 'lib/common/models/menuLinkCollection.js';
import AdminMenuLinksEditView from './views/adminMenuLinksEditView.js';
import adminMenuLinksListTableLayoutTpl from './views/adminMenuLinksListTableLayoutTpl.hbs!';
import adminMenuLinksRowTpl from './views/adminMenuLinksRowTpl.hbs!';

import SkuCollection from 'lib/inventory/models/skuCollection.js';
import AdminSkusEditView from './views/adminSkusEditView.js';
import adminSkusListTableLayoutTpl from './views/adminSkusListTableLayoutTpl.hbs!';
import adminSkusRowTpl from './views/adminSkusRowTpl.hbs!';

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

import CommodityCollection from 'lib/inventory/models/commodityCollection.js';
import AdminCommoditiesEditView from './views/adminCommoditiesEditView.js';
import adminCommoditiesListTableLayoutTpl from './views/adminCommoditiesListTableLayoutTpl.hbs!';
import adminCommoditiesRowTpl from './views/adminCommoditiesRowTpl.hbs!';

import UnitTypeCollection from 'lib/inventory/models/unitTypeCollection.js';
import AdminUnitTypesEditView from './views/adminUnitTypesEditView.js';
import adminUnitTypesListTableLayoutTpl from './views/adminUnitTypesListTableLayoutTpl.hbs!';
import adminUnitTypesRowTpl from './views/adminUnitTypesRowTpl.hbs!';

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

import ClientCollection from  'lib/accounting/models/clientCollection.js';
import AdminClientsEditView from  './views/adminClientsEditView.js';
import adminClientsListTableLayoutTpl from  './views/adminClientsListTableLayoutTpl.hbs!';
import adminClientsRowTpl from  './views/adminClientsRowTpl.hbs!';

import CustomerCollection from  'lib/accounting/models/customerCollection.js';
import AdminCustomersEditView from  './views/adminCustomersEditView.js';
import adminCustomersListTableLayoutTpl from  './views/adminCustomersListTableLayoutTpl.hbs!';
import adminCustomersRowTpl from  './views/adminCustomersRowTpl.hbs!';

import InboundOrderCollection from  'lib/accounting/models/inboundOrderCollection.js';
import AdminInboundOrdersEditView from  './views/adminInboundOrdersEditView.js';
import adminInboundOrdersListTableLayoutTpl from  './views/adminInboundOrdersListTableLayoutTpl.hbs!';
import adminInboundOrdersRowTpl from  './views/adminInboundOrdersRowTpl.hbs!';

import OutboundOrderCollection from  'lib/accounting/models/outboundOrderCollection.js';
import AdminOutboundOrdersEditView from  './views/adminOutboundOrdersEditView.js';
import adminOutboundOrdersListTableLayoutTpl from  './views/adminOutboundOrdersListTableLayoutTpl.hbs!';
import adminOutboundOrdersRowTpl from  './views/adminOutboundOrdersRowTpl.hbs!';

export default Marionette.Object.extend({
   index(){
    this.users();
  },
  organizations(id){
    let adminIndexView =  new AdminIndexView();
    let organizationCollection = Radio.channel('data').request('collection', OrganizationCollection, {doFetch: false});
    let isCreatable = false;
    let myself =  Radio.channel('data').request('myself');
    if(myself.isGrantedRole('ROLE_DEV')){
      isCreatable = true;
    }
    let entityViewOptions = {
      isCreatable: isCreatable,
      listLength: 20,
      entityId: id,
      collection: organizationCollection,
      searchPath: ['id', 'name'],
      EditView: AdminOrganizationsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminOrganizationsListTableLayoutTpl,
      entityRowTpl: adminOrganizationsRowTpl,
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
  offices(id){
    let adminIndexView =  new AdminIndexView();
    let officeCollection = Radio.channel('data').request('collection', OfficeCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: officeCollection,
      searchPath: ['id', 'name'],
      EditView: AdminOfficesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminOfficesListTableLayoutTpl,
      entityRowTpl: adminOfficesRowTpl,
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
  departments(id){
    let adminIndexView =  new AdminIndexView();
    let departmentCollection = Radio.channel('data').request('collection', DepartmentCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: departmentCollection,
      searchPath: ['id', 'name', 'office.name'],
      EditView: AdminDepartmentsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminDepartmentsListTableLayoutTpl,
      entityRowTpl: adminDepartmentsRowTpl,
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
  menuLinks(id){
    let adminIndexView =  new AdminIndexView();
    let menuLinkCollection = Radio.channel('data').request('collection', MenuLinkCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: menuLinkCollection,
      searchPath: ['id', 'name', 'url'],
      EditView: AdminMenuLinksEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminMenuLinksListTableLayoutTpl,
      entityRowTpl: adminMenuLinksRowTpl,
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
  skus(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let skuCollection = Radio.channel('data').request('collection', SkuCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: skuCollection,
      searchPath: ['name','number','label'],
      EditView: AdminSkusEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminSkusListTableLayoutTpl,
      entityRowTpl: adminSkusRowTpl,
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
    Radio.channel('help').trigger('show', 'parts');
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
  commodities(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let commodityCollection = Radio.channel('data').request('collection', CommodityCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: commodityCollection,
      searchPath: ['name','commodityId','commodityAltId'],
      EditView: AdminCommoditiesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminCommoditiesListTableLayoutTpl,
      entityRowTpl: adminCommoditiesRowTpl,
      colspan: 6
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
  unitTypes(id){
    let adminInventoryIndexView =  new AdminInventoryIndexView();
    let unitTypeCollection = Radio.channel('data').request('collection', UnitTypeCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: unitTypeCollection,
      searchPath: ['name'],
      EditView: AdminUnitTypesEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminUnitTypesListTableLayoutTpl,
      entityRowTpl: adminUnitTypesRowTpl,
      colspan: 6
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
  clients(id){
    let adminAccountingIndexView =  new AdminAccountingIndexView();
    let clientCollection = Radio.channel('data').request('collection', ClientCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: clientCollection,
      searchPath: ['id', 'name'],
      EditView: AdminClientsEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminClientsListTableLayoutTpl,
      entityRowTpl: adminClientsRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminAccountingIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminAccountingIndexView);
  },
  customers(id){
    let adminAccountingIndexView =  new AdminAccountingIndexView();
    let customerCollection = Radio.channel('data').request('collection', CustomerCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: customerCollection,
      searchPath: ['id', 'name'],
      EditView: AdminCustomersEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminCustomersListTableLayoutTpl,
      entityRowTpl: adminCustomersRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminAccountingIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminAccountingIndexView);
  },
  inboundOrders(id){
    let adminAccountingIndexView =  new AdminAccountingIndexView();
    let inboundOrderCollection = Radio.channel('data').request('collection', InboundOrderCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: inboundOrderCollection,
      searchPath: ['id', 'label', 'client.name'],
      EditView: AdminInboundOrdersEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminInboundOrdersListTableLayoutTpl,
      entityRowTpl: adminInboundOrdersRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminAccountingIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminAccountingIndexView);
  },
  outboundOrders(id){
    let adminAccountingIndexView =  new AdminAccountingIndexView();
    let outboundOrderCollection = Radio.channel('data').request('collection', OutboundOrderCollection, {doFetch: false});
    let entityViewOptions = {
      isCreatable: true,
      listLength: 20,
      entityId: id,
      collection: outboundOrderCollection,
      searchPath: ['id', 'label', 'client.name'],
      EditView: AdminOutboundOrdersEditView,
      useTableView: true,
      usePagination: 'server',
      entityListTableLayoutTpl: adminOutboundOrdersListTableLayoutTpl,
      entityRowTpl: adminOutboundOrdersRowTpl,
      colspan: 6
    };

    let entityView = new EntityIndexView(entityViewOptions);

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', entityView]]),
        viewInstance: adminAccountingIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', adminAccountingIndexView);
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