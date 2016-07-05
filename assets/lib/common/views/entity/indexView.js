'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'marionette';
import Radio from 'backbone.radio';

import viewTpl from './indexView.hbs!';
import SearchableListLayoutView from './searchableListLayoutView';


export default Marionette.View.extend({
  initialize(options){
    if(!options.EditView){
      throw 'Must pass the Entity Index view a EditView constructor option';
    }
    this.EditView = options.EditView;

    if(!this.model){
      this.model = new Backbone.Model({
        isCreatable: true
      });
    }
    this.listenTo(this.model, "change:isCreatable", this.checkCreateButtonVisiblity);

    if(!this.collection){
      throw 'Must pass the Entity Index view a collection';
    }
  },
  serializeData(){
    return {
      entityUrl: this.collection.url(),
      title: this.collection.title,
      isCreatable: this.model.get('isCreatable')
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
  },
  ui: {
    'entityListLink': 'a.entity-list-link',
    'createButton': '.create-entity-button'
  },
  events: {
    'click @ui.entityListLink': 'showListLinkClicked',
    'click @ui.createButton': 'createEntityButtonClicked',
  },
  childViewEvents: {
    'show:view': 'showView',
    'show:list': 'showList',
    'show:logs': 'showLogs',
    'show:log': 'showLog',
    'select:model': 'showEdit',
    'add:entity': 'addEntity',
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
  showView(childView, args, options){
    this.getRegion('action').show(args.view, options);
  },
  showList(childView, args, options){
    if(this.options.isCreatable === false){
      this.model.set('isCreatable', false);
    }else{
      this.model.set('isCreatable', true);
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