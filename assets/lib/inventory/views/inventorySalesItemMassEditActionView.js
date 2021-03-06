'use strict';

import _ from 'underscore';
import jquery from 'jquery';
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import Uuid from 'uuid/v4';

import RemoteSearchSelect2Behavior from 'lib/common/behaviors/remoteSearchSelect2.js';

import viewTpl from './inventorySalesItemMassEditActionView.hbs!';

import OutboundOrderCollection from 'lib/accounting/models/outboundOrderCollection.js';
import BinCollection from '../models/binCollection.js';
import SkuCollection from '../models/skuCollection.js';

import SalesItemModel from '../models/salesItemModel.js';
import MassSalesItemModel from '../models/massSalesItemModel.js';

export default Marionette.View.extend({
  initialize(){
    this.selectedCollection = Radio.channel('inventory').request('get:isSelected:salesItem');
  },
  template: viewTpl,
  ui: {
    'attributeSelect': 'select[name="updateAttribute"]',
    'updateTypeRadio': 'input[name="updateType"]',
    'controlLabel': '[data-ui="controlLabel"]',
    'controlContainer': '[data-ui="controlContainer"]',
    'form': 'form',
    'submitButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'errorContainer': '[data-ui="errorContainer"]'
  },
  events: {
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
    'change @ui.attributeSelect': 'updateControl',
    'click @ui.updateTypeRadio': 'updateControl'
  },
  select2Options:{
    outboundOrder:{
      collection: OutboundOrderCollection,
      url: OutboundOrderCollection.prototype.selectOptionsUrl,
      search: 'label',
      textProperty: 'label'
    },
    bin:{
      collection: BinCollection,
      url: BinCollection.prototype.selectOptionsUrl,
      search: 'name'
    },
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
  save(event){
    event.preventDefault();
    this.disableButtons();
    setTimeout(()=>{
      this.editSalesItems().then(()=>{
        this.enableButtons();
        Radio.channel('dialog').trigger('close');
        Radio.channel('inventory').trigger('refresh:list:salesItem');
      }).catch((err)=>{
        this.ui.errorContainer.removeClass('is-hidden').show().text(err).fadeOut(3000);
        this.enableButtons();
      });
    }, 5);
  },
  updateControl(){
    let updatableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
    let attribute = this.ui.attributeSelect.val();
    let type = this.ui.updateTypeRadio.filter(':checked').val();
    if(!attribute || !type){
      return;
    }
    this.ui.controlLabel.text(updatableAttributes[attribute].title);
    if(type === 'single'){
      if(updatableAttributes[attribute].type === 'select'){
        let $select = jquery('<select name="'+attribute+'">');
        this.ui.controlContainer.empty().append($select);
        RemoteSearchSelect2Behavior.prototype.setupSelect2($select, this.select2Options[attribute]);
      }else{
        let $input = jquery('<input name="'+attribute+'" type="'+updatableAttributes[attribute].type+'" />');
        this.ui.controlContainer.empty().append($input);
      }
    }else{
      let $input = jquery('<textarea rows="10" name="'+attribute+'" ></textarea>');
      this.ui.controlContainer.empty().append($input);
    }
  },
  editSalesItems(){
    return new Promise((resolve, reject)=>{
      let updatableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
      let attr = Syphon.serialize(this);
      let type = this.ui.updateTypeRadio.filter(':checked').val();
      let valuePromise = null;
      let attrKey = null;
      _.each(attr, (value, key)=>{
        if(key.indexOf('update') < 0){
          attrKey = key;
          if(type === 'single'){
            valuePromise = this.getSingleValue(value, key);
          }else{
            valuePromise = this.getValuesArray(value, key);
          }
        }
      });
      valuePromise.then((updateValue)=>{
        if(Array.isArray(updateValue)){
          this.selectedCollection.each((travelerId, index)=>{
            travelerId.set(attrKey, updateValue[index]);
          });
        }else{
          this.selectedCollection.each((travelerId)=>{
            travelerId.set(attrKey, updateValue);
          });
        }
        //set an Id so backbone does a "put" rather than "post"
        let massSalesItem = MassSalesItemModel.findOrCreate({
          id: Uuid()
        });
        massSalesItem.set('type', 'edit');
        massSalesItem.get('salesItems').reset(this.selectedCollection.models);
        massSalesItem.save().done(()=>{
          resolve();
        });
      }).catch((err)=>{
        reject(err);
      });
    });
  },
  getSingleValue(valueStr, attribute){
    let updatableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
    let updateValue = null;
    return new Promise((resolve, reject)=>{
      if(updatableAttributes[attribute].type === 'select'){
        updateValue = this.select2Options[attribute].collection.prototype.model.findOrCreate({id: parseInt(valueStr)});
      }else{
        updateValue = valueStr;
      }
      resolve(updateValue);
    });
  },
  getValuesArray(valueStr, attribute){
    let updatableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
    let valuesLookup = {};
    let rawValuesArray = [];
    let valuesArray = [];
    _.each(valueStr.split('\n'), (value)=>{
      let trimmedValue = value.trim();
      if(trimmedValue){
        rawValuesArray.push(trimmedValue);
        if(!valuesLookup[trimmedValue]){
          valuesLookup[trimmedValue] = true;
        }
      }
    });
    if(rawValuesArray.length !== this.selectedCollection.length){
      throw 'Selected Sales Item Count and Suppplied Values Count do not match';
    }
    return new Promise((resolve, reject)=>{
      this.getAjaxResult(valuesLookup, attribute, (err)=>{
        if(err){
          reject(err);
        }else{
          _.each(rawValuesArray, (value)=>{
            let trimmedValue = value.trim();
            valuesArray.push(valuesLookup[trimmedValue]);
          });
          resolve(valuesArray);
        }
      });
    });
  },
  getAjaxResult(valuesLookup, attribute, callback){
    let updatableAttributes = SalesItemModel.prototype.getUpdatadableAttributes();
    if(updatableAttributes[attribute].type === 'select'){
      let search = this.select2Options[attribute].search;
      let terms = _.keys(valuesLookup);
      jquery.ajax({
        url: this.select2Options[attribute].url,
        dataType: 'json',
        data:{
          terms: terms.join(','),
          search: search,
          page: 1,
          per_page: 1000
        },
      }).done((data)=>{
        _.each(data.list, (attrs)=>{
          valuesLookup[attrs[search]] = this.select2Options[attribute].collection.prototype.model.findOrCreate(attrs);
        });
        _.each(valuesLookup, (value, key)=>{
          if(!(value instanceof this.select2Options[attribute].collection.prototype.model)){
            callback('Missing Value for "'+key+'"');
          }
        });
        callback();
      });
    }else{
      _.each(valuesLookup, (value, key)=>{
        valuesLookup[key] = key;
      });
      callback();
    }
  },
  disableButtons(){
    this.ui.submitButton.prop('disabled', true).addClass('is-loading');
    this.ui.cancelButton.prop('disabled', true);
  },
  enableButtons(){
    this.ui.submitButton.prop('disabled', false).removeClass('is-loading');
    this.ui.cancelButton.prop('disabled', false);
  },
});