"use strict";

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import appLayoutTpl from './appLayoutView.hbs!';
import DialogRegion from 'lib/common/regions/dialogRegion.js';

import NavLayoutView from 'lib/common/views/navLayoutView.js'
import MenuLayoutView from 'lib/common/views/menuLayoutView.js';
import DefaultView from 'lib/common/views/defaultView.js';
import MenuStaticView from 'lib/common/views/menuStaticView.js';
import HelpView from 'lib/common/views/helpView.js';

import MyselfModel from 'lib/common/models/myselfModel.js';

export default Marionette.View.extend({
  initialize(){
    //this.listenTo(Radio.channel('app'), 'change:menuItems', this._showMenuItem);
    this.listenTo(Radio.channel('app'), 'show:view', this._showView);
    this.listenTo(Radio.channel('dialog'), 'open', this._openDialog);
    this.listenTo(Radio.channel('dialog'), 'opened', this._dialogOpened);
    this.listenTo(Radio.channel('dialog'), 'close', this._closeDialog);
    this.listenTo(Radio.channel('dialog'), 'closed', this._dialogClosed);
    this.listenTo(Radio.channel('help'), 'show', this._showHelp);
  },
  template: appLayoutTpl,
  ui: {
    dialog: '#dialog'
  },
  regions: {
    nav: {
      el: '.nav',
      replaceElement: true
    },
    menu: {
      el: '.menu',
      replaceElement: true
    },
    help: '#help-panel',
    main: "#main-section",
    dialogContent: DialogRegion,
    footer: '.footer'
  },
  onRender(){
    let myself = Radio.channel('data').request('myself');
    this.showChildView('nav', new NavLayoutView({
      model: myself
    }));
    this.showChildView('main', new DefaultView({model: myself}));
    this.showChildView('menu', new MenuStaticView());
    myself.fetch();
    this.ui.dialog.dialog({
      autoOpen: false,
      modal: true,
      close: function( event, ui ) {
        Radio.channel('dialog').trigger('closed');
      },
      open: function( event, ui ) {
        Radio.channel('dialog').trigger('opened');
      }
    });
  },
  _showView(view){
    this.showChildView('main', view);
  },
  _showMenuItem(menuItemcollection){
    let menuLayoutView = new MenuLayoutView({
      collection: menuItemcollection,
    });
    this.showChildView('menu', menuLayoutView);
  },
  _openDialog(view, options){
    options = _.extend({modal:true}, options);
    this.showChildView('dialogContent', view);
    this.ui.dialog.dialog('option', options);
    this.ui.dialog.dialog('open');
  },
  _dialogOpened(){
    //no-op
  },
  _closeDialog(){
    this.ui.dialog.dialog('close');
  },
  _dialogClosed(){
    this.getRegion('dialogContent').reset();
  },
  _showHelp(helpItemName){
    let helpItem = Radio.channel('help').request('get', helpItemName);
    this.showChildView('help', new HelpView({
      model: new Backbone.Model(helpItem)
    }));
  }
});