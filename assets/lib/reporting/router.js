'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

//for jspm for now
import './controller.js';

export default Marionette.Object.extend({
  initialize(options){
    this.options = {

    };
    Object.assign(this.options, options);
    if(!(this.options.appRouter instanceof Marionette.AppRouter)){
      throw 'Controller Must be passed an AppRouter instance.';
    }else{
      this.appRouter = this.options.appRouter;
    }
    this.appRouter.processAppRoutes(this, this.routes);
  },
  routes:  {
    'reporting': 'index',
    'reporting/single_query_report/:id': 'singleQueryReport',

  },
  index(){
    this.singleQueryReport();
  },
  singleQueryReport(id){
    Radio.channel('app').trigger('request:started');
    System.import('lib/reporting/controller.js').then((controllerModule)=>{
      Radio.channel('app').trigger('request:finished');
      let controller = new controllerModule.default();
      controller.singleQueryReport(id);
    });
  },
});