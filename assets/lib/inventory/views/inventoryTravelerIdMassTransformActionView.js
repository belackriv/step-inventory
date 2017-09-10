'use strict';

import _ from 'underscore';
import jquery from 'jquery';
import Backbone from 'backbone'
import Syphon from 'backbone.syphon';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import Uuid from 'uuid/v4';

import RemoteSearchSelect2Behavior from 'lib/common/behaviors/remoteSearchSelect2.js';

import viewTpl from './inventoryTravelerIdMassTransformActionView.hbs!';

import SkuCollection from '../models/skuCollection.js';
import InventoryTravelerIdTransformModel from '../models/inventoryTravelerIdTransformModel.js';
import MassTravelerIdModel from '../models/massTravelerIdModel.js';
import TravelerIdModel from '../models/travelerIdModel.js';
import SalesItemModel from '../models/salesItemModel.js';

export default Marionette.View.extend({
  initialize(){
    this.selectedCollection = Radio.channel('inventory').request('get:isSelected:travelerId');
    this.model = new Backbone.Model();
  },
   behaviors: {
    'RemoteSearchSelect2': {
      sku:{
        url: SkuCollection.prototype.selectOptionsUrl,
        search: 'name',
        placeholder: 'Select SKU'
      }
    },
  },
  template: viewTpl,
  ui: {
    'form': 'form',
    'typeInput': 'input[name="type"]',
    'fromCountInput': 'input[name="fromCount"]',
    'toCountInput': 'input[name="toCount"]',
    'submitButton': 'button[data-ui-name="save"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'errorContainer': '[data-ui="errorContainer"]'
  },
  events: {
    'change @ui.typeInput': 'typeChanged',
    'submit @ui.form ': 'save',
    'click @ui.cancelButton': 'cancel',
  },
  serializeData(){
    let data = {};
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
      this.transformTravelerIds().then(()=>{
        this.enableButtons();
        Radio.channel('dialog').trigger('close');
        Radio.channel('inventory').trigger('refresh:list:travelerId');
      }).catch((err)=>{
        this.ui.errorContainer.removeClass('is-hidden').show().text(err).fadeOut(3000);
        this.enableButtons();
      });
    }, 5);
  },
  typeChanged(event){
    event.preventDefault();
    let attr = Syphon.serialize(this);
    if(attr.type === 'oneToOne'){
      this.ui.fromCountInput.val('1').addClass('is-disabled').prop('disabled', true);
      this.ui.toCountInput.val('1').addClass('is-disabled').prop('disabled', true);
    }else if(attr.type === 'breakdown'){
      this.ui.fromCountInput.val('1').addClass('is-disabled').prop('disabled', true);
      this.ui.toCountInput.val('').removeClass('is-disabled').prop('disabled', false);
    }else if(attr.type === 'consolidate'){
      this.ui.fromCountInput.val('').removeClass('is-disabled').prop('disabled', false);
      this.ui.toCountInput.val('1').addClass('is-disabled').prop('disabled', true);
    }
  },
  transformTravelerIds(){
    return new Promise((resolve, reject)=>{
      let attr = Syphon.serialize(this);
      if(!attr.target){
        throw 'Must select a target';
      }
      if(!attr.quantity){
        throw 'Must supply a Quantity';
      }
      if(!attr.sku){
        throw 'Must supply a SKU';
      }
      if(attr.type === 'consolidate' && (this.selectedCollection.length * attr.quantity) % this.ui.fromCountInput.val() !== 0){
        throw 'Selected count must be divisible by "From" Count';
      }
      let sku = SkuCollection.prototype.model.findOrCreate({id: parseInt(attr.sku)});
      this.validateQuantities(attr);
      let transforms = new Backbone.Collection();
      this.selectedCollection.each((travelerId, idx)=>{
        if(travelerId.get('transform')){
          throw 'TravelerId "'+travelerId.get('label')+'" has already been transformed';
        }
        let transform = null;
        if(attr.type === 'consolidate'){
          //need a way to let the server know of references / ids cid maybe?
          let transform = transforms.at(Math.floor(idx/attr.fromCount));
          if(!transform){
            transform = this.createTransform(attr, sku, travelerId);
            transforms.add(transform);
          }
          transform.get('fromTravelerIds').add(travelerId);
        }else{
          let transform = this.createTransform(attr, sku, travelerId);
          transforms.add(transform);
        }
      });
      //set an Id so backbone does a "put" rather than "post"
      let massTravelerId = MassTravelerIdModel.findOrCreate({
        id: Uuid(),
      });
      massTravelerId.set('type', 'transform');
      massTravelerId.get('travelerIds').reset(this.selectedCollection.models);
      massTravelerId.save().done(()=>{
        resolve();
      }).fail(()=>{
        let transformsArray = transforms.toArray();
        for (let i = 0; i < transformsArray.length; i++) {
          transformsArray[i].destroy();
        }
      });
    });
  },
  validateQuantities(attr){
    let quantity = parseFloat(attr.quantity);
    let toCount = parseInt(attr.toCount);
    let fromCount = parseInt(attr.fromCount);
    /*if(attr.type === 'breakdown'){
      this.selectedCollection.each((travelerId)=>{
        if(parseFloat(travelerId.get('quantity')) <  quantity){
          throw 'TravelerId "'+travelerId.get('label')+'" only has '+travelerId.get('quantity')+' of SKU '+travelerId.get('sku').get('name');
        }
      });
    }else*/
    if(attr.type === 'consolidate'){
      let availableQuantity = 0;
      this.selectedCollection.each((travelerId)=>{
        availableQuantity += parseFloat(travelerId.get('quantity'));
      });
      if(availableQuantity < quantity){
        throw 'Only "'+availableQuantity+'" total available quantity';
      }
    }else{
      this.selectedCollection.each((travelerId)=>{
        if(parseFloat(travelerId.get('quantity')) < quantity){
          throw 'TravelerId "'+travelerId.get('label')+'" only has '+travelerId.get('quantity')+' of SKU '+travelerId.get('sku').get('name');
        }
      });
    }
  },
  createTransform(attr, sku, travelerId){
    let transform = InventoryTravelerIdTransformModel.build({
      quantity: attr.quantity
    });
    transform.set('cid', transform.cid);
    travelerId.set('transform', transform);
    let toCount = parseInt(attr.toCount);
    for(let i = 0; i < toCount; i++){
      if(attr.target === 'travelerId'){
        let newTravelerId = TravelerIdModel.build({
          inboundOrder: travelerId.get('inboundOrder'),
          bin: travelerId.get('bin'),
          sku: sku,
          quantity: attr.quantity
        });
        transform.get('toTravelerIds').add(newTravelerId);
      }else{
        let newSalesItem = SalesItemModel.build({
          bin: travelerId.get('bin'),
          sku: sku,
          quantity: attr.quantity
        })
        transform.get('toSalesItems').add(newSalesItem);
      }
    }
    return transform;
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
