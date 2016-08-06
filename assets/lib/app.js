'use strict';

import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import AppLayoutView from './appLayoutView.js';
import AppMainRouter from './appMainRouter.js';
import AppDataService from './appDataService.js';

export default Marionette.Application.extend({
  initialize(){
    this.router = new AppMainRouter();
    this.dataService = new AppDataService();
    this.listenTo(Radio.channel('app'), 'navigate', this.navigate);
    this.listenTo(Radio.channel('app'), 'request:started', this.requestStarted);
    this.listenTo(Radio.channel('app'), 'request:finished', this.requestFinished);
    Radio.channel('app').reply('currentRoute', this.getCurrentRoute);
  },
  //baseUrl: '/~belac/stepthrough/app_dev.php',
  baseUrl: '',
  wsAddress: '192.168.1.7:8080',
  currentRequests: 0,
  navigate(route,  options){
    options = options ||  {
      trigger: true
    };
    route = route.replace(this.baseUrl, '');
    Backbone.history.navigate(route, options);
    Radio.channel('app').trigger('route:changed', route);
  },
  getCurrentRoute(){
    return Backbone.history.fragment;
  },
  region: '#content',
  onStart(){
    this.showView( new AppLayoutView() );

    if(Backbone.history){
     var routeFound = Backbone.history.start({
        pushState: true,
        root: this.baseUrl
      });
      Radio.channel('app').trigger('route:changed', this.getCurrentRoute());
      console.log('starting route "'+this.getCurrentRoute()+'" found: '+routeFound);
    }
  },
  requestStarted(){
    this.currentRequests++;
    Radio.channel('app').trigger('loading:show');
  },
  requestFinished(){
    this.currentRequests--;
    if(this.currentRequests < 1){
      Radio.channel('app').trigger('loading:hide');
    }
  },
});


