'use strict';

import jquery from 'jquery';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';
import viewTpl from './singleQueryReportListView.hbs!';
import childViewTpl from './singleQueryReportListRowView.hbs!'

import SingleQueryReportCollection from '../models/singleQueryReportCollection.js';
import SearchableListTableView from 'lib/common/views/entity/searchableListTableView.js';


export default Marionette.View.extend({
  initialize(options){
    this.reportCollection =  Radio.channel('data').request('collection', SingleQueryReportCollection, {fetchAll: true});
  },
  template: viewTpl,
  regions: {
    'tbody': 'tbody',
  },
  ui:{
    'collapsedBar': '[data-ui="collapsedBar"]',
    'table': '[data-ui="table"]',
  },
  events: {
    'click @ui.collapsedBar': 'showList',
  },
  childViewEvents: {
    'select:model': 'selectModel',
    'button:click': 'buttonClick',
    'link:click': 'linkClick'
  },
  onRender(){
    this.ui.collapsedBar.hide();
    this.showChildView('tbody', new SearchableListTableView({
      collection: this.reportCollection,
      childViewOptions: {
        template: childViewTpl
      },
      colspan: 2
    }));
  },
  showList(){
    this.ui.collapsedBar.hide();
    this.ui.table.show();
    this.model.set('report', null);
  },
  selectModel(childView, args){
    this.ui.collapsedBar.show();
    this.ui.table.hide();
    this.model.set('report', args.model);
  },
  buttonClick(){

  },
  linkClick(){

  }
});