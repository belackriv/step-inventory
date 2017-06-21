"use strict";

import _ from 'underscore';
import jquery from 'jquery';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';
import Papa from 'papa';

import viewTpl from './massImportView.hbs!';
import tableViewTpl from './massImportTableLayoutTpl.hbs!';
import rowViewTpl from './massImportRowTpl.hbs!';
import SearchableListLayoutView from 'lib/common/views/entity/searchableListLayoutView.js';
import BaseSearchableModel from 'lib/common/models/baseSearchableModel.js';
import BaseSearchableCollection from 'lib/common/models/baseSearchableCollection.js';

Papa.SCRIPT_PATH = '/assets/jspm_packages/npm/papaparse@4.3.3/papaparse.js';

export default Marionette.View.extend({
  initialize(){

  },
  template: viewTpl,
  regions: {
    table: '[data-region="csv-table"]'
  },
  ui: {
    'dropTarget': '[data-ui="dropTarget"]',
    'openFileButton': 'button[name="openFile"]',
    'csvFileInput': 'input[name="csvFile"]',
    'importButton': 'button[data-ui-name="import"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
  },
  events:{
    'click @ui.openFileButton': 'onOpenFileButtonClick',
    'change @ui.csvFileInput': 'addSelectedFile',
    'click @ui.importButton': 'import',
    'click @ui.cancelButton': 'cancel'
  },
  onRender(){
    let dropTarget = this.ui.dropTarget.get(0);
    ['dragenter','dragover','dragleave','dragstop','drop'].forEach(
      event => dropTarget.addEventListener(event, this.stopNormalBehavior.bind(this), false));
    ['dragenter','dragover'].forEach(
      event => dropTarget.addEventListener(event, this.showFileHover.bind(this), false));
    ['drop','dragleave','dragstop'].forEach(
      event => dropTarget.addEventListener(event, this.hideFileHover.bind(this), false));
    dropTarget.addEventListener('drop', this.openDroppedFile.bind(this), false);
  },
  stopNormalBehavior(event){
    event.preventDefault();
    event.stopPropagation();
  },
  showFileHover(){
    this.ui.dropTarget.css('background-color', 'lightgreen');
  },
  hideFileHover(){
    this.ui.dropTarget.css('background-color', '');
  },
  onOpenFileButtonClick(event){
    this.ui.csvFileInput.click();
  },
  openDroppedFile(event){
    this.openFile(event.dataTransfer.files);
  },
  openSelectedFile(event){
    this.openFile(event.target.files);
  },
  openFile(files){
    this.disableFormButtons();
    let viewCollection = new BaseSearchableCollection();
    let columns = [];
    let childSerializeData = function(model){
      model = model || this.model;
      let data = {_columns: []};
      _.each(columns, (val)=>{
        data._columns.push(model.get(val));
      });
      return data;
    };
    let serializeData = function(){
      let data = {_columns: []};
      _.each(columns, (val)=>{
        data._columns.push(val);
      });
      return data;
    };
    let viewOptions = {
      listLength: 20,
      collection: viewCollection,
      searchPath: columns,
      useTableView: true,
      usePagination: true,
      entityListTableLayoutTpl: tableViewTpl,
      entityRowTpl: rowViewTpl,
      colspan: 0,
      childViewOptions: {
        serializeData: childSerializeData
      },
      serializeData: serializeData
    };
    let viewShown = false;
    Papa.parse(files[0], {
      worker: true,
      header: true,
      step: (row)=>{
        if(!viewShown){
          viewOptions.colspan = row.meta.fields.length + 1;
          _.each(row.meta.fields, (col)=>{
            columns.push(col);
          });
          viewShown = true
        }
        viewCollection.add(new BaseSearchableModel(row.data[0]));
      },
      complete: ()=>{
        this.model.set('items', viewCollection);
        this.showChildView('table', new SearchableListLayoutView(viewOptions));
        this.enableFormButtons();
      }
    });
  },
  import(event){
    this.disableFormButtons();
    this.model.save({
      type: this.model.get('typeModel').prototype.importData.type
    }).then(()=>{
      this.enableFormButtons();
    }).catch(()=>{
      this.enableFormButtons();
    });
  },
  cancel(event){
    this.triggerMethod('show:list');
  },
  disableFormButtons(){
    this.ui.openFileButton.addClass('is-loading').prop('disabled', true);
    this.ui.importButton.addClass('is-loading').prop('disabled', true);
    this.ui.cancelButton.addClass('is-disabled').prop('disabled', true);
  },
  enableFormButtons(){
    this.ui.openFileButton.removeClass('is-loading').prop('disabled', false);
    this.ui.importButton.removeClass('is-loading').prop('disabled', false);
    this.ui.cancelButton.removeClass('is-disabled').prop('disabled', false);
  },
  onChildviewButtonClick(childView, args){
    let $button = jquery(args.button).closest('button');
    let methodName = $button.data('method');
    this[methodName](args);
  },
  removeItem(args){
    args.model.destroy();
  }
});