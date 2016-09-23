'use strict';

import jquery from 'jquery';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportBasicView.hbs!';

import LoadingView from 'lib/common/views/loadingView.js';
import ReportFormLayoutView from './singleQueryReportFormLayoutView.js';
import ReportTableLayoutView from './singleQueryReportTableLayoutView.js';

import SingleQueryReportCollection from '../models/singleQueryReportCollection.js';
import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

export default Marionette.View.extend({
  initialize(options){
    this.model = new Backbone.Model({report: null, isLoading: false});
    this.listenTo(this.model, 'change:report', this.reportChanged);
    this.reportCollection =  Radio.channel('data').request('collection', SingleQueryReportCollection, {fetchAll: true});
  },
  behaviors: {
    'Stickit': {},
  },
  template: viewTpl,
  regions: {
    'form': '#single-query-report-form',
    'table': '#single-query-report-table',
  },
  ui:{
    'reportSelect': 'select[name="report"]',
    'runReportButton': 'button[name="runReport"]',
    'exportReportButton': 'button[name="exportReport"]',
    'exportReportCsvButton': 'button[name="exportReportCsv"]',
  },
  bindings: {
    '@ui.reportSelect': {
      observe: 'report',
      useBackboneModels: true,
      selectOptions:{
        labelPath: 'attributes.name',
        collection: 'this.reportCollection',
        defaultOption: {
          label: 'Choose one...',
          value: null
        }
      }
    },
  },
  events: {
    'click @ui.runReportButton': 'runReport',
    'click @ui.exportReportButton': 'exportReport',
    'click @ui.exportReportCsvButton': 'exportReportCsv'
  },
  reportChanged(){
    this.showChildView('form', new ReportFormLayoutView({
      model: this.model.get('report'),
    }));
  },
  runReport(event){
    if(!this.model.get('isLoading')){
      let queryParams = Radio.channel('reporting').request('singleQueryReport:queryParams');
      let ReportPageableCollection = BaseUrlBaseCollection.extend({
        url: this.model.get('report').url()+'/run',
        //queryParams: queryParams,
      });
      let reportPageableCollection = new ReportPageableCollection();
      this.showChildView('table', new LoadingView());
      this.model.set('isLoading', true);
      reportPageableCollection.fetch({data: queryParams}).always(()=>{
        this.model.set('isLoading', false);
        this.showChildView('table', new ReportTableLayoutView({
          collection: reportPageableCollection,
          model: this.model,
          columns: this.model.get('report').get('columns')
        }));
      });
    }
  },
  exportReport(event){
    if(!this.model.get('isLoading')){
      window.location = this.model.get('report').url()+'/export?'+jquery.param(
        Radio.channel('reporting').request('singleQueryReport:queryParams')
      );
    }
  },
  exportReportCsv(event){
    if(!this.model.get('isLoading')){
      window.location = this.model.get('report').url()+'/export_csv?'+jquery.param(
        Radio.channel('reporting').request('singleQueryReport:queryParams')
      );
    }
  },
});