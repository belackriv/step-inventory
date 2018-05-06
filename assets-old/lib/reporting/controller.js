'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import ReportingIndexView from './views/reportingIndexView.js';
import SingleQueryReportBasicView from './views/singleQueryReportBasicView.js';


export default Marionette.Object.extend({
  singleQueryReport(id){
    let reportingIndexView =  new ReportingIndexView();
    let singleQueryReportBasicView = new SingleQueryReportBasicView({id:id});

    this.buildViewStack([
      {
        regionViewMap: new Map([['content', singleQueryReportBasicView]]),
        viewInstance: reportingIndexView
      }
    ]);

    Radio.channel('app').trigger('show:view', reportingIndexView);
    Radio.channel('help').trigger('show', 'singleQueryReport');
  },
  buildViewStack(stack){
    for(let viewObj of stack){
      for(let [regionName, viewInstance] of viewObj.regionViewMap){
        viewObj.viewInstance.once('render', ()=>{
          viewObj.viewInstance.showChildView(regionName, viewInstance);
        });
      }
    }
  }
});