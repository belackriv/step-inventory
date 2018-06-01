'use strict';

import cssSrc from '../css/step-inventory.css!text';
import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import AppLayoutView from './appLayoutView.js';
import AppMainRouter from './appMainRouter.js';
import AppDataService from './appDataService.js';
import AppHelpService from './appHelpService.js';

export default Marionette.Application.extend({
  initialize(){
    this.router = new AppMainRouter();
    this.dataService = new AppDataService();
    this.helpService = new AppHelpService();
    this.listenTo(Radio.channel('app'), 'print', this.print);
    this.listenTo(Radio.channel('app'), 'navigate', this.navigate);
    this.listenTo(Radio.channel('app'), 'request:started', this.requestStarted);
    this.listenTo(Radio.channel('app'), 'request:finished', this.requestFinished);
    Radio.channel('app').reply('currentRoute', this.getCurrentRoute);
  },
  baseUrl: '',
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
  region: '#app',
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
  print(html, options){
    options = _.extend({
      height: screen.height,
      width: screen.width,
      title: 'Print'
    },options);
    let printWindow = window.open('', 'print', 'height='+options.height+',width='+options.width);
    printWindow.document.write('<html><head><title>'+options.title+'</title>');
    printWindow.document.write('<style type="text/css">'+cssSrc+'</style>');
    printWindow.document.write('</head><body >');
    printWindow.document.write(html);
    printWindow.document.write('</body></html>');

    printWindow.document.close(); // necessary for IE >= 10
    printWindow.focus(); // necessary for IE >= 10

    printWindow.print();
    printWindow.close();
  }
});


