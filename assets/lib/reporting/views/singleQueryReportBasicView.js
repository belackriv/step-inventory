'use strict';

import jquery from 'jquery';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportBasicView.hbs!';

import LoadingView from 'lib/common/views/loadingView.js';
import ReportListView from './singleQueryReportListView.js';
import ReportFormLayoutView from './singleQueryReportFormLayoutView.js';
import ReportTableLayoutView from './singleQueryReportTableLayoutView.js';

import BaseUrlBaseCollection from 'lib/common/models/baseUrlBaseCollection.js';

export default Marionette.View.extend({
  initialize(options){
    this.model = new Backbone.Model({report: null, isLoading: false});
    this.listenTo(this.model, 'change:report', this.reportChanged);
  },
  template: viewTpl,
  regions: {
    'list': '#single-query-report-list',
    'form': '#single-query-report-form',
    'table': '#single-query-report-table',
  },
  ui:{
    'formContainer': '#single-query-report-form-container',
    'runReportButton': 'button[name="runReport"]',
    'exportReportButton': 'button[name="exportReport"]',
    'exportReportCsvButton': 'button[name="exportReportCsv"]',
  },
  events: {
    'click @ui.runReportButton': 'runReport',
    'click @ui.exportReportButton': 'exportReport',
    'click @ui.exportReportCsvButton': 'exportReportCsv'
  },
  onRender(){
    this.showChildView('list', new ReportListView({
      model: this.model,
    }));
    this.reportChanged();
  },
  reportChanged(){
    this.getRegion('table').empty();
    this.getRegion('form').empty();
    this.ui.formContainer.hide();
    if(this.model.get('report')){
      this.ui.formContainer.show();
      this.showChildView('form', new ReportFormLayoutView({
        model: this.model.get('report'),
      }));
    }
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