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
    'validationErrors': '[data-ui="validationErrors"]',
    'importButton': 'button[data-ui-name="import"]',
    'exportButton': 'button[data-ui-name="export"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
  },
  events:{
    'click @ui.openFileButton': 'onOpenFileButtonClick',
    'change @ui.csvFileInput': 'addSelectedFile',
    'click @ui.importButton': 'import',
    'click @ui.exportButton': 'export',
    'click @ui.cancelButton': 'cancel'
  },
  onRender(){
    this.ui.validationErrors.hide();
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
    this.initializeValidationErrors();
    let viewCollection = this.viewCollection = new BaseSearchableCollection();
    let columns = [];
    let childSerializeData = function(model){
      model = model || this.model;
      let data = {attributes: {}};
      _.each(columns, (val)=>{
        data.attributes[val] = model.get(val);
      });
      data.rowNum = model.get('_rowNum');
      data.error = model.get('_error');
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
        let model = new BaseSearchableModel(row.data[0]);
        viewCollection.add(model);
        model.set('_rowNum', viewCollection.indexOf(model) + 1);
        this.validateModel(model)
      },
      complete: ()=>{
        this.model.set('items', viewCollection);
        this.showValidationErrors();

        this.showChildView('table', new SearchableListLayoutView(viewOptions));
        this.enableFormButtons();
      }
    });
  },
  validateModel(model){
    _.each(this.model.get('typeModel').prototype.importData.properties, (property)=>{
      if(property.required){
        if(model.get(property.name) === undefined || model.get(property.name) === null || model.get(property.name) === ''){
          model.set('_error', true);
          this.validationErrors.push('Missing required property "'+property.name+'" at row '+model.get('_rowNum'));
        }
      }
    });
  },
  validateCollection(collection){
    collection.each((model)=>{
      this.validateModel(model);
    });
  },
  initializeValidationErrors(){
    this.validationErrors = [];
    this.ui.validationErrors.hide();
    this.ui.validationErrors.find('ul').empty();
  },
  showValidationErrors(){
    if(this.validationErrors.length > 0){
      this.ui.validationErrors.removeClass('is-success').addClass('is-danger').find('p').text('Found '+this.validationErrors.length+' errors.');
      _.find(this.validationErrors, (error, idx)=>{
        if(idx >= 10){
          this.ui.validationErrors.find('ul').append('<li>..And '+(this.validationErrors.length - 10)+' more not shown.</li>');
          return true;
        }
        this.ui.validationErrors.find('ul').append('<li>'+error+'</li>');
      });
    }else{
      this.ui.validationErrors.removeClass('is-danger').addClass('is-success').find('p').text('No Errors Found.');
    }
    this.ui.validationErrors.show();
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
  export(event){
    window.location = '/export/'+ this.model.get('typeModel').prototype.importData.type;
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
    args.model.set('id', null);
    args.model.destroy();
    this.initializeValidationErrors();
    this.validateCollection(this.viewCollection);
    this.showValidationErrors();
  }
});