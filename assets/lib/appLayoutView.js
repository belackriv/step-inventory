"use strict";

import Marionette from 'marionette';
import Radio from 'backbone.radio';

import appLayoutTpl from './appLayoutView.hbs!';

import NavLayoutView from 'lib/common/views/navLayoutView.js'
import MenuLayoutView from 'lib/common/views/menuLayoutView.js';
import DefaultView from 'lib/common/views/defaultView.js';

import UserModel from 'lib/common/models/userModel.js';

export default Marionette.View.extend({
  initialize(){
    this.listenTo(Radio.channel('app'), 'change:menuItems', this._showMenuItem);
    this.listenTo(Radio.channel('app'), 'show:view', this._showView);
  },
  template: appLayoutTpl,
  regions: {
    nav: {
      el: '.nav',
      replaceElement: true
    },
    menu: {
      el: '.menu',
      replaceElement: true
    },
    main: "#main-section",
    footer: '.footer'
  },
  onRender(){
    let myself = new UserModel();
    this.showChildView('nav', new NavLayoutView({
      model: myself
    }));
    this.showChildView('main', new DefaultView());
    //myself.fetch();
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
});