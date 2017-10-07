'use strict';

import _ from 'underscore';
import jquery from 'jquery';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventorySalesItemMassSelectionActionView.hbs!';
import SalesItemModel from '../models/salesItemModel.js';

export default Marionette.View.extend({
  initialize(){
    this.selectedCollection = Radio.channel('inventory').request('get:isSelected:salesItem');
  },
  template: viewTpl,
  ui: {
    'modeRadio': 'input[name="mode"]',
    'salesItemsInput': 'textarea[name="salesItems"]',
    'count': '[data-ui="count"]',
    'selectedCountLabel': '[data-ui="countLabel"]',
    'form': 'form',
    'saveButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'exportButton': 'button[data-ui-name="export"]',
    'errorContainer': '[data-ui="errorContainer"]'
  },
  events: {
    'change @ui.salesItemsInput': 'salesItemsChanged',
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
    'click @ui.exportButton': 'export',
  },
  serializeData(){
    let data = {};
    data.updateableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
    data.selectedCount = this.selectedCollection.length;
    return data;
  },
  cancel(){
    Radio.channel('dialog').trigger('close');
  },
  export(){
    let element = document.createElement('a');
    let csvText = 'Label,Serial,SKU,Outbound Order,Bin\n';
    this.selectedCollection.each((salesItem)=>{
      csvText += salesItem.get('label')+',';
      csvText += salesItem.get('serial')+',';
      csvText += salesItem.get('sku').get('label')+',';
      csvText += salesItem.get('outboundOrder')?salesItem.get('outboundOrder').get('label')+',':',';
      csvText += salesItem.get('bin').get('name');
      csvText += '\n';
    });
    element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvText));
    element.setAttribute('download', 'selection.csv');

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
  },
  save(event){
    event.preventDefault();
    this.disableButtons();
    let mode = this.ui.modeRadio.filter(':checked').val();
    let promise = null;
    if(mode === 'add'){
      promise = this.addSalesItemsToSelection();
    }else if(mode === 'remove'){
      promise = this.removeSalesItemsFromSelection();
    }else if(mode === 'replace'){
     this.clearSelection();
      promise = this.addSalesItemsToSelection();
    }
    if(promise){
      promise.then(()=>{
        Radio.channel('dialog').trigger('close');
      }).catch((err)=>{
        this.ui.errorContainer.removeClass('is-hidden').show().text(err).fadeOut(3000);
        this.enableButtons();
      });
    }else{
      this.enableButtons();
    }
  },
  salesItemsChanged(){
    this.ui.count.text(this.getSalesItemsFromInput().length);
  },
  getSalesItemsFromInput(){
    let salesItemsArray = [];
    _.each(this.ui.salesItemsInput.val().split('\n'), (value)=>{
      let trimmedValue = value.trim();
      if(trimmedValue){
        salesItemsArray.push(trimmedValue);
      }
    });
    return salesItemsArray;
  },
  addSalesItemsToSelection(){
    return new Promise((resolve, reject)=>{
      let salesItems = this.getSalesItemsFromInput();
      jquery.ajax({
        url: SalesItemModel.prototype.urlRoot(),
        dataType: 'json',
        data:{
          terms: salesItems.join(','),
          search: 'label',
          page: 1,
          per_page: 1000
        },
      }).done((data)=>{
        _.each(data.list, (attrs)=>{
          let salesItem = SalesItemModel.findOrCreate(attrs);
          salesItem.set('isSelected', true);
        });
        _.each(salesItems, (salesItem)=>{
          let travlerIdModel = this.selectedCollection.findWhere({label: salesItem})
          if(!travlerIdModel){
            reject('No Traveler Id found for "'+salesItem+'"');
          }
        });
        resolve();
      });
    });
  },
  removeSalesItemsFromSelection(){
    return new Promise((resolve, reject)=>{
      let salesItems = this.getSalesItemsFromInput();
      let salesItemModels = [];
      this.selectedCollection.each((salesItem)=>{
        if(salesItems.indexOf(salesItem.get('label')) > -1){
          salesItemModels.push(salesItem);
        }
      });
      _.invoke(salesItemModels, 'set', 'isSelected', false);
      resolve();
    });
  },
  clearSelection(){
    let salesItem = this.selectedCollection.toArray();
    _.invoke(salesItem, 'set', 'isSelected', false);
    this.selectedCollection.reset();
  },
  disableButtons(){
    this.ui.saveButton.addClass('is-loading').prop('disabled', true);
    this.ui.cancelButton.prop('disabled', true);
  },
  enableButtons(){
    this.ui.saveButton.removeClass('is-loading').prop('disabled', false);
    this.ui.cancelButton.prop('disabled', false);
  },
});