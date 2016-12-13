"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import viewTpl from './profileView.hbs!';
import AccountView from './accountView.js';
import LoadingView from './loadingView.js';
import AccountModel from '../models/accountModel.js';
import AccountCollection from '../models/accountCollection.js';

export default Marionette.View.extend({
  initialize(){
    let myself = Radio.channel('data').request('myself');
    this.model = myself;
  },
  template: viewTpl,
  className: 'box',
  regions:{
    'accountInfo': '[data-region="accountInfo"]'
  },
  behaviors: {
    'Stickit': {},
  },
  ui: {
    'userInfoForm': 'form[data-ui="userInfo"]',
    'usernameInput': 'input[name="username"]',
    'emailInput': 'input[name="email"]',
    'firstNameInput': 'input[name="firstName"]',
    'lastNameInput': 'input[name="lastName"]',
    'accountInfoTabs': '[data-ui="accountInfoTabs"]',
    'syncStatusIndicator': '.not-synced-alert',
    'updateButton': 'button[data-ui-name="update"]',
    'revertButton': 'button[data-ui-name="revert"]'
  },
  bindings: {
    '@ui.usernameInput': 'username',
    '@ui.emailInput': 'email',
    '@ui.firstNameInput': 'firstName',
    '@ui.lastNameInput': 'lastName',
  },
  events:{
    'submit @ui.userInfoForm': 'updateProfile',
    'click @ui.revertButton': 'revertUserInfoForm'
  },
  modelEvents:{
    'change:username': 'showNotSynced',
    'change:email': 'showNotSynced',
    'change:firstName': 'showNotSynced',
    'change:lastName': 'showNotSynced',
    'change:organization': 'render'
  },
  onRender(){
    this.ui.syncStatusIndicator.hide();
    if(this.model.isAccountOwner()){
      this.ui.accountInfoTabs.show();
      this.renderAccountInfo();
    }else{
      this.ui.accountInfoTabs.hide();
    }
  },
  renderAccountInfo(){
    if(!this.account){
      this.account = true;
      this.showChildView('accountInfo', new LoadingView());
      let accounts = new AccountCollection();
      accounts.fetch().done(()=>{
        this.account = accounts.first();
        this.showChildView('accountInfo', new AccountView({
          model: this.account
        }));
      });
    }
  },
  revertUserInfoForm(){
    this.disableFormButtons();
    this.model.fetch().done(()=>{
      this.enableFormButtons();
      this.ui.syncStatusIndicator.hide();
    });
  },
  showNotSynced(){
    this.ui.syncStatusIndicator.show();
  },
  updateProfile(event){
    event.preventDefault();
    this.disableFormButtons();
    let profileModel = new Backbone.Model(this.model.attributes);
    profileModel.url = '/profile';
    profileModel.save().always(()=>{
      this.enableFormButtons();
      this.revertUserInfoForm();
    })
  },
  disableFormButtons(){
    this.ui.updateButton.addClass('is-loading').prop('disabled', true);
    this.ui.revertButton.addClass('is-disabled').prop('disabled', true);
  },
  enableFormButtons(){
    this.ui.updateButton.removeClass('is-loading').prop('disabled', false);
    this.ui.revertButton.removeClass('is-disabled').prop('disabled', false);
  },
});