"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './accountView.hbs!';

import UserCollection from '../models/userCollection.js';
import SubscriptionCollection from '../models/subscriptionCollection.js';
import AccountOwnerChangeModel from '../models/accountOwnerChangeModel.js';
import AccountSubscriptionChangeModel from '../models/accountSubscriptionChangeModel.js';

import NoChildrenRowView from 'lib/common/views/noChildrenRowView.js';
import AccountChangeItemView from './accountChangeItemView.js';
import BillItemView from './billItemView.js';

export default Marionette.View.extend({
  initialize(){
    this.setInitialProperties();
  },
  template: viewTpl,
  behaviors: {
    'Stickit': {},
  },
  regions:{
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
    'subscriptionSelect': 'select[name="subscription"]',
    'subscriptionDesc': '[data-ui="subscriptionDesc"]',
    'changeOwnerButton': 'button[data-ui="changeOwner"]',
    'changeSubscriptionButton': 'button[data-ui="changeSubscription"]',
  },
  events:{
    'click @ui.changeOwnerButton': 'changeOwner',
    'click @ui.changeSubscriptionButton': 'changeSubscription',
  },
  modelEvents:{
    'change:organization': 'setInitialProperties',
    'change:subscription': 'subscriptionChanged'
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
    '@ui.subscriptionSelect': {
      observe: 'subscription',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection(){
          let collection = Radio.channel('data').request('collection', SubscriptionCollection, {fetchAll: true});
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
  subscriptionChanged(){
    this.ui.subscriptionDesc.text(this.model.get('subscription').get('description'));
  },
  onRender(){
    this.subscriptionChanged();
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
    });
  },
  changeSubscription(event){
    event.preventDefault();
    this.disableButtons('changeSubscriptionButton');
    let accountSubscriptionChange = AccountSubscriptionChangeModel.findOrCreate({
      account: this.model,
      oldSubscription: this.initialProperties.subscription,
      newSubscription: this.model.get('subscription'),
    });
    accountSubscriptionChange.save().always(()=>{
      this.enableButtons();
    });
  },
  disableButtons(loadingButtonName){
    this.ui.changeOwnerButton.addClass('is-disabled').prop('disabled', true);
    this.ui.changeSubscriptionButton.addClass('is-disabled').prop('disabled', true);
    this.ui[loadingButtonName].removeClass('is-disabled').addClass('is-loading');
  },
  enableButtons(){
    this.ui.changeOwnerButton.removeClass('is-disabled is-loading').prop('disabled', false);
    this.ui.changeSubscriptionButton.removeClass('is-disabled is-loading').prop('disabled', false);
  }
});