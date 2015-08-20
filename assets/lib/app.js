'use strict';

import Backbone from 'backbone';
import Marionette from 'marionette';
import 'lib/shims/marionette.radio.shim';
import appModuleList from 'lib/app_module_list';
import appLayoutTpl from 'lib/app_layout.hbs!';


var StepThrough = new Marionette.Application();
StepThrough.baseURL = '/~belac/stepthrough/app_dev.php';
StepThrough.wsAddress = '192.168.1.7:8080';

StepThrough.navigate = function(route,  options){
  options = options ||  {};
  Backbone.history.navigate(route, options);
};

StepThrough.getCurrentRoute = function(){
  return Backbone.history.fragment;
};

StepThrough.startSubApp = function(appName, args, callback){
  var currentApp = appName ? StepThrough.module(appName) : null;
  if (StepThrough.currentApp === currentApp){ return; }

  if (StepThrough.currentApp){
    StepThrough.currentApp.stop();
  }

  StepThrough.currentApp = currentApp;
  if(currentApp){
    currentApp.start(args);
  }
};

StepThrough.AppLayout = Marionette.LayoutView.extend({
  template: appLayoutTpl,
  el: "#content",
  regions: {
    navbar: "nav",
    header: "header",
    main: "#main-section",
    footer: 'footer'
  }
});

StepThrough.on("start", function(){
  StepThrough.appLayout = new StepThrough.AppLayout();
  StepThrough.appLayout.render();

  if(Backbone.history){
    require(appModuleList, function () {
     

      var routeFound = Backbone.history.start({
        pushState: true,
        root: StepThrough.baseURL
      });
      console.log('starting route "'+StepThrough.getCurrentRoute()+'" found: '+routeFound);
    });
  }
  
});

export default StepThrough;
