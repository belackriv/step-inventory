'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './indexView.hbs!';
import SearchableListLayoutView from './searchableListLayoutView';

import MassImportModel from 'lib/common/models/massImportModel.js';
import MassImportView from 'lib/common/views/massImportView.js';

//"jspm": "0.17.0-beta.32",
export default Marionette.View.extend({
  initialize(options){
    if(!options.EditView){
      throw 'Must pass the Entity Index view a EditView constructor option';
    }
    this.EditView = options.EditView;

    if(!this.model){
      this.model = new Backbone.Model({
        isCreatable: true,
        isImportable: false,
      });
    }
    this.listenTo(this.model, "change:isCreatable", this.checkCreateButtonVisiblity);
    this.listenTo(this.model, "change:isImportable", this.checkImportButtonVisiblity);

    if(!this.collection){
      throw 'Must pass the Entity Index view a collection';
    }
  },
  serializeData(){
    const title = this.options.title||this.collection.title||'';
    return {
      entityUrl: this.collection.url(),
      title: title,
      isCreatable: this.model.get('isCreatable'),
      isImportable: this.model.get('isImportable'),
    };
  },
  template: viewTpl,
  regions: {
    action: '.entity-action-pane'
  },
  onRender(){
    if(this.options.usePagination !== 'server'){
      this.collection.fetch();
    }
    if(this.options.entityId){
      var model = this.collection.model.findOrCreate({id: this.options.entityId});
      model.fetch();
      if(typeof this.options.logEntryId !== 'undefined'){
        if(this.options.logEntryId){
          var logCollection = new LogCollection({
            baseModelInstance: model
          });
          if(!model.logs){
            model.logs = logCollection;
          }
          var logEntryModel = new LogModel({id:this.options.logEntryId});
          logEntryModel.baseModelInstance = model;
          logEntryModel.fetch();
          logCollection.fetch().done(()=>{
            model.logs.reset(logCollection.models);
            logEntryModel.trigger('change');
          });
          this.showLog({model: model}, {model: logEntryModel});
        }else{
          this.showLogs(null, {model: model});
        }
      }else{
        this.showEdit(null, {model: model});
      }
    }else{
      this.showList();
    }
    this.checkCreateButtonVisiblity();
    this.checkImportButtonVisiblity();
  },
  ui: {
    'entityListLink': 'a.entity-list-link',
    'createButton': '.create-entity-button',
    'importButton': '.import-entity-button'
  },
  events: {
    'click @ui.entityListLink': 'showListLinkClicked',
    'click @ui.createButton': 'createEntityButtonClicked',
    'click @ui.importButton': 'importEntityButtonClicked',
  },
  childViewEvents: {
    'show:view': 'showView',
    'show:list': 'showList',
    'show:logs': 'showLogs',
    'show:log': 'showLog',
    'select:model': 'showEdit'
  },
  checkCreateButtonVisiblity(){
    if(this.isRendered){
      if(this.model.get('isCreatable')){
        this.ui.createButton.show();
      }else{
        this.ui.createButton.hide();
      }
    }
  },
  checkImportButtonVisiblity(){
    if(this.isRendered){
      if(this.model.get('isImportable')){
        this.ui.importButton.show();
      }else{
        this.ui.importButton.hide();
      }
    }
  },
  showListLinkClicked(event){
    event.preventDefault();
    this.showList();
  },
  createEntityButtonClicked(event){
    event.preventDefault();
    let newEntityDefaults = _.extend({isLocked: false}, this.options.newEntityDefaults);
    let model = new this.collection.model(newEntityDefaults);
    this.showEdit(null, {model:model}, {preventDestroy: true});
  },
  importEntityButtonClicked(event){
    event.preventDefault();
    let importModel = new MassImportModel({
      typeModel: this.collection.model
    });
    let importView = new MassImportView({
      model: importModel
    });
    this.showView(null, {view: importView});
  },
  showView(childView, args, options){
    this.getRegion('action').show(args.view, options);
  },
  showList(childView, args, options){
    if(this.options.isCreatable === false){
      this.model.set('isCreatable', false);
    }else{
      this.model.set('isCreatable', true);
    }
    if(this.options.isImportable === true){
      this.model.set('isImportable', true);
    }else{
      this.model.set('isImportable', false);
    }
    let viewOptions = _.extend(this.options, {
      collection: this.collection,
    });
    this.getRegion('action').show(new SearchableListLayoutView(viewOptions), options);
    Radio.channel('app').trigger('navigate', this.collection.url(), {trigger: false});
  },
  onShowEdit(childView, args, options){
    this.showEdit(childView, args, options);
  },
  showEdit(childView, args, options){
    this.model.set('isCreatable', false);
    this.model.set('isImportable', false);
    var editView = new this.EditView({
      model: args.model,
      parentView: this
    });
    this.getRegion('action').show(editView, options);
    let url = args.model.url();
    if(typeof editView.entityUrl === 'function'){
      url = editView.entityUrl();
    }
    Radio.channel('app').trigger('navigate', url, {trigger: false});
  },
  showLogs(childView, args, options){
    this.model.set('isCreatable', false);
    this.model.set('isImportable', false);
    var logCollection = new LogCollection(null, {
      baseModelInstance: args.model
    });
    if(!args.model.logs){
      args.model.logs = logCollection;
    }
    this.getRegion('action').show(new LogLayoutView({
      collection: args.model.logs,
      model: args.model,
      returnModel: childView.options.returnModel
    }), options);

    logCollection.requestTrackingFetch(this.model).done(()=>{
      args.model.logs.reset(logCollection.models);
    });
    Radio.channel('app').trigger('navigate', logCollection.url(), {trigger: false});
  },
  showLog(childView, args, options){
    this.model.set('isCreatable', false);
    this.model.set('isImportable', false);
    if(!args.model.baseModelInstance){
      args.model.baseModelInstance = childView.model;
    }
    var logView = new LogView({
      model: args.model,
      parentView: this,
      returnModel: childView.options.returnModel
    });
    this.getRegion('action').show(logView, options);
    Radio.channel('app').trigger('navigate', args.model.url(), {trigger: false});
  },
  addEntity(childView){
    this.collection.add(childView.model);
  }
});