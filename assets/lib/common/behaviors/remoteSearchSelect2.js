'use strict';

import _ from 'underscore';
import jquery from 'jquery';
import Backbone from 'backbone';
import Marionette from 'marionette';

export default Marionette.Behavior.extend({
  ui:{
    'select': 'select'
  },
  onAttach(){
    this.findSelects();
  },
  onDomRefresh(){
    this.findSelects();
  },
  findSelects(){
    _.each(this.ui.select, (elem)=>{
      let $elem = jquery(elem);
      if(this.options[$elem.attr('name')]){
        this.listenTo(this.view.model, 'change:'+$elem.attr('name'), this.setSelect2Value.bind(this, $elem));
        if(this.view.model.get($elem.attr('name'))){
          this.setSelect2Value($elem, this.view.model, this.view.model.get($elem.attr('name')));
        }
        this.setupSelect2($elem, this.options[$elem.attr('name')]);
      }
    });
  },
  setupSelect2(ui, options){
    ui.select2({
      selectOnClose: true,
      minimumInputLength: 3,
      dropdownAutoWidth : true,
      width: '200px',
      ajax: {
        url: options.url,
        dataType: 'json',
        delay: 250,
        data(params){
          let terms = params.term.split(' ');
          let pageNum = params.page?params.page+1:1;
          return {
            terms: terms.join(','),
            search: options.search,
            page: pageNum,
            per_page: 20
          };
        },
        processResults(data){
          let textProperty = options.textProperty?options.textProperty:'name';
          let results = _.map(data.list, (obj)=>{
            return {id: obj.id, text: obj[textProperty]}
          });
          return {
            results: results
          };
        }
      }
    });
  },
  setSelect2Value(ui, model, newValue){
    ui.empty();
    if(newValue){
      let value = newValue;
      let label = newValue;
      if(value instanceof Backbone.Model){
        let options =  this.options[ui.attr('name')];
        let textProperty = options.textProperty?options.textProperty:'name';
        label = value.get(textProperty);
        value = value.id;
      }
      let option = jquery('<option value="'+value+'">'+label+'</option>');
      ui.append(option);
      ui.change();
    }
  }
});