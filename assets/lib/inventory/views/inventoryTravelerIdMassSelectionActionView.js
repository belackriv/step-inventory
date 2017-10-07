'use strict';

import _ from 'underscore';
import jquery from 'jquery';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './inventoryTravelerIdMassSelectionActionView.hbs!';
import TravelerIdModel from '../models/travelerIdModel.js';

export default Marionette.View.extend({
  initialize(){
    this.selectedCollection = Radio.channel('inventory').request('get:isSelected:travelerId');
  },
  template: viewTpl,
  ui: {
    'modeRadio': 'input[name="mode"]',
    'travelerIdsInput': 'textarea[name="travelerIds"]',
    'count': '[data-ui="count"]',
    'selectedCountLabel': '[data-ui="countLabel"]',
    'form': 'form',
    'saveButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'exportButton': 'button[data-ui-name="export"]',
    'errorContainer': '[data-ui="errorContainer"]'
  },
  events: {
    'change @ui.travelerIdsInput': 'travelerIdsChanged',
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
    'click @ui.exportButton': 'export',
  },
  serializeData(){
    let data = {};
    data.updateableAttributes = TravelerIdModel.prototype.getUpdatadableAttributes();
    data.selectedCount = this.selectedCollection.length;
    return data;
  },
  cancel(){
    Radio.channel('dialog').trigger('close');
  },
  export(){
    let element = document.createElement('a');
    let csvText = 'Label,Serial,SKU,Inbound Order,Bin\n';
    this.selectedCollection.each((travelerId)=>{
      csvText += travelerId.get('label')+',';
      csvText += travelerId.get('serial')+',';
      csvText += travelerId.get('sku').get('label')+',';
      csvText += travelerId.get('inboundOrder').get('label')+',';
      csvText += travelerId.get('bin').get('name');
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
      promise = this.addTravelerIdsToSelection();
    }else if(mode === 'remove'){
      promise = this.removeTravelerIdsFromSelection();
    }else if(mode === 'replace'){
     this.clearSelection();
      promise = this.addTravelerIdsToSelection();
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
  travelerIdsChanged(){
    this.ui.count.text(this.getTravelerIdsFromInput().length);
  },
  getTravelerIdsFromInput(){
    let travelerIdsArray = [];
    _.each(this.ui.travelerIdsInput.val().split('\n'), (value)=>{
      let trimmedValue = value.trim();
      if(trimmedValue){
        travelerIdsArray.push(trimmedValue);
      }
    });
    return travelerIdsArray;
  },
  addTravelerIdsToSelection(){
    return new Promise((resolve, reject)=>{
      let travelerIds = this.getTravelerIdsFromInput();
      jquery.ajax({
        url: TravelerIdModel.prototype.urlRoot(),
        dataType: 'json',
        data:{
          terms: travelerIds.join(','),
          search: 'label',
          page: 1,
          per_page: 1000
        },
      }).done((data)=>{
        _.each(data.list, (attrs)=>{
          let travelerId = TravelerIdModel.findOrCreate(attrs);
          travelerId.set('isSelected', true);
        });
        _.each(travelerIds, (travelerId)=>{
          let travlerIdModel = this.selectedCollection.findWhere({label: travelerId})
          if(!travlerIdModel){
            reject('No Traveler Id found for "'+travelerId+'"');
          }
        });
        resolve();
      });
    });
  },
  removeTravelerIdsFromSelection(){
    return new Promise((resolve, reject)=>{
      let travelerIds = this.getTravelerIdsFromInput();
      let travelerIdModels = [];
      this.selectedCollection.each((travelerId)=>{
        if(travelerIds.indexOf(travelerId.get('label')) > -1){
          travelerIdModels.push(travelerId);
        }
      });
      _.invoke(travelerIdModels, 'set', 'isSelected', false);
      resolve();
    });
  },
  clearSelection(){
    let tids = this.selectedCollection.toArray();
    _.invoke(tids, 'set', 'isSelected', false);
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