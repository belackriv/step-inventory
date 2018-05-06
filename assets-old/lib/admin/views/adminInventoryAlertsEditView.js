"use strict";

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from  "./adminInventoryAlertsEditView.hbs!";
import OfficeCollection from 'lib/common/models/officeCollection.js';
import DepartmentCollection from 'lib/common/models/departmentCollection.js';
import SkuCollection from 'lib/inventory/models/skuCollection.js';

export default Marionette.View.extend({
  template: viewTpl,
  behaviors: {
    'Stickit': {},
    'ShowNotSynced': {},
    'SetNotSynced': {},
    'SaveCancelDelete': {},
    'RemoteSearchSelect2': {
      sku:{
        url: SkuCollection.prototype.selectOptionsUrl,
        search: 'name'
      }
    },
  },
  ui: {
    'departmentSelect': 'select[name="department"]',
    'skuSelect': 'select[name="sku"]',
    'isActiveInput': 'input[name="isActive"]',
    'countInput': 'input[name="count"]',
    'typeSelect': 'select[name="type"]',
    'runAlertButton': 'button[data-ui="run"]',
    'alertResults': 'span[data-ui="alertResults"]'
  },
  events:{
    'change @ui.skuSelect': 'skuChanged',
    'click @ui.runAlertButton': 'runAlert',
  },
  bindings: {
    '@ui.countInput': 'count',
    '@ui.typeSelect': 'type',
    '@ui.isActiveInput': 'isActive',
    '@ui.departmentSelect': {
      observe: 'department',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', DepartmentCollection, {doFetch: false});
          let officeCollection = Radio.channel('data').request('collection', OfficeCollection, {doFetch: false});
          this.listenTo(officeCollection, 'add', (office)=>{
            collection.add(office.get('departments').models);
          });
          officeCollection.each((office)=>{
            collection.add(office.get('departments').models);
          });
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    }
  },
  skuChanged(){
    this.model.set('sku', SkuCollection.prototype.model.findOrCreate({id: parseInt(this.ui.skuSelect.val())}));
  },
  runAlert(){
    this.ui.runAlertButton.prop('disabled', true);
    this.ui.alertResults.text('...');
    this.model.runAlert().then((results)=>{
      this.ui.runAlertButton.prop('disabled', false);
      this.ui.alertResults.text(results.alertsRun+' alerts run, '+results.alertsFound+' alerts found, '+results.alertsSent+' alerts sent.');
    }).catch((err)=>{
      this.ui.runAlertButton.prop('disabled', false);
    });
  }
});
