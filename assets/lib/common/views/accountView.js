"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './accountView.hbs!';

import UserCollection from '../models/userCollection.js';
import PlanCollection from '../models/planCollection.js';
import SubscriptionModel from '../models/subscriptionModel.js';
import AccountOwnerChangeModel from '../models/accountOwnerChangeModel.js';
import AccountPlanChangeModel from '../models/accountPlanChangeModel.js';

import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';
import PaymentSourceItemView from './paymentSourceItemView.js';
import AccountChangeItemView from './accountChangeItemView.js';
import BillItemView from './billItemView.js';

import PaymentInfoView from './paymentInfoView.js';

export default Marionette.View.extend({
  initialize(){
    this.setInitialProperties();
  },
  template: viewTpl,
  behaviors: {
    'Stickit': {},
  },
  regions:{
    'paymentSources': {
      el: 'tbody[data-region="paymentSources"]',
      replaceElement: true
    },
    'changeHistory': {
      el: 'tbody[data-region="changeHistory"]',
      replaceElement: true
    },
    'billingHistory': {
      el: 'tbody[data-region="billingHistory"]',
      replaceElement: true
    }
  },
  ui: {
    'ownerSelect': 'select[name="owner"]',
    'planSelect': 'select[name="plan"]',
    'planDesc': '[data-ui="planDesc"]',
    'changeOwnerButton': 'button[data-ui="changeOwner"]',
    'changePlanButton': 'button[data-ui="changePlan"]',
    'addPaymentInfoButton': 'button[data-ui="addPaymentInfo"]',
  },
  events:{
    'click @ui.changeOwnerButton': 'changeOwner',
    'click @ui.changePlanButton': 'changePlan',
    'click @ui.addPaymentInfoButton': 'openAddPaymentInfoDialog'
  },
  modelEvents:{
    'change:organization': 'setInitialProperties',
    'change:newPlan': 'planChanged'
  },
  bindings: {
    '@ui.ownerSelect': {
      observe: 'owner',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.username',
        collection(){
          let collection = Radio.channel('data').request('collection', UserCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
    '@ui.planSelect': {
      observe: 'subscription',
      useBackboneModels: true,
      onGet(value){
        return value.get('plan');
      },
      updateModel(value){
        this.model.set('newPlan', value);
        return false;
      },
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', PlanCollection, {fetchAll: true});
          return collection;
        },
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
  setInitialProperties(){
    this.initialProperties = {
      owner: this.model.get('owner'),
      subscription: this.model.get('subscription'),
    };
  },
  planChanged(){
    this.ui.planDesc.text(this.model.get('newPlan').get('description'));
  },
  onRender(){
    if(this.model.get('subscription') && this.model.get('subscription').get('plan')){
      this.ui.planDesc.text(this.model.get('subscription').get('plan').get('description'));
    }
    this.showChildView('paymentSources', new Marionette.CollectionView({
      collection: this.model.get('paymentSources'),
      childView: PaymentSourceItemView,
      tagName: 'tbody',
      emptyView: NoChildrenRowView,
      emptyViewOptions:{
        colspan: 4
      }
    }));
    this.showChildView('changeHistory', new Marionette.CollectionView({
      collection: this.model.get('accountChanges'),
      childView: AccountChangeItemView,
      tagName: 'tbody',
      emptyView: NoChildrenRowView,
      emptyViewOptions:{
        colspan: 6
      }
    }));
    this.showChildView('billingHistory', new Marionette.CollectionView({
      collection: this.model.get('bills'),
      childView: BillItemView,
      tagName: 'tbody',
      emptyView: NoChildrenRowView,
      emptyViewOptions:{
        colspan: 3
      }
    }));
  },
  changeOwner(event){
    event.preventDefault();
    this.disableButtons('changeOwnerButton');
    let accountOwnerChange = AccountOwnerChangeModel.findOrCreate({
      account: this.model,
      oldOwner: this.initialProperties.owner,
      newOwner: this.model.get('owner'),
    });
    accountOwnerChange.save().always(()=>{
      this.enableButtons();
    }).done(()=>{
      this.model.get('accountChanges').add(accountOwnerChange);
    });
  },
  changePlan(event){
    event.preventDefault();
    this.disableButtons('changePlanButton');
    let accountPlanChange = AccountPlanChangeModel.findOrCreate({
      account: this.model,
      oldPlan: this.model.get('subscription').get('plan'),
      newPlan: this.model.get('newPlan'),
    });
    accountPlanChange.save().always(()=>{
      this.enableButtons();
    }).done(()=>{
      this.model.get('accountChanges').add(accountPlanChange);
    });
  },
  openAddPaymentInfoDialog(event){
    event.preventDefault();
    var options = {
      title: 'Add Payment Info',
      width: '400px'
    };
    let view = new PaymentInfoView({
      model: this.model
    });
    Radio.channel('dialog').trigger('close');
    Radio.channel('dialog').trigger('open', view, options);
  },
  disableButtons(loadingButtonName){
    this.ui.changeOwnerButton.addClass('is-disabled').prop('disabled', true);
    this.ui.changePlanButton.addClass('is-disabled').prop('disabled', true);
    this.ui[loadingButtonName].removeClass('is-disabled').addClass('is-loading');
  },
  enableButtons(){
    this.ui.changeOwnerButton.removeClass('is-disabled is-loading').prop('disabled', false);
    this.ui.changePlanButton.removeClass('is-disabled is-loading').prop('disabled', false);
  }
});