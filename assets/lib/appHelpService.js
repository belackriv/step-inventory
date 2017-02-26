'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import HelpTopicCollection from 'lib/common/models/helpTopicCollection.js';


export default Marionette.Object.extend({
  initialize(options){
    Radio.channel('help').reply('get', this.getHelpItem.bind(this));
    this.helpItems = new HelpTopicCollection(this.initialTopics);
  },
  getHelpItem(itemName){
    return new Promise((resolve, reject)=>{
      let item = this.helpItems.find({name:itemName});
      if(!item){
        let collection = new HelpTopicCollection();
        collection.fetch({data: {terms: itemName, search: 'name'}}).then(()=>{
          this.helpItems.add(collection.models);
          item = this.helpItems.find({name:itemName});
          if(!item){
            item = this.helpItems.find({name:'noHelpFound'});
          }
          resolve(item);
        });
      }else{
        resolve(item);
      }
    });
  },
  helpItems: null,
  initialTopics: [
    {name: 'noHelpFound', heading: 'Help Missing', content: 'No help content was found for topic '},
    {name: 'helpTopics', heading: 'Help Topics', content:
      'These are the currently bound topics:'+
      '<ul>'+
      '<li>organizations</li>'+
      '<li>users</li>'+
      '<li>offices</li>'+
      '<li>departments</li>'+
      '<li>announcements</li>'+
      '<li>menuItems</li>'+
      '<li>menuLinks</li>'+
      '<li>helpTopics</li>'+
      '<li>skus</li>'+
      '<li>parts</li>'+
      '<li>partCategories</li>'+
      '<li>partGroups</li>'+
      '<li>commodities</li>'+
      '<li>unitTypes</li>'+
      '<li>binTypes</li>'+
      '<li>bins</li>'+
      '<li>inventoryMovementRules</li>'+
      '<li>clients</li>'+
      '<li>customers</li>'+
      '<li>inboundOrders</li>'+
      '<li>outboundOrders</li>'+
      '<li>travelerIds</li>'+
      '<li>salesItems</li>'+
      '<li>inventoryTravelerIdEdits</li>'+
      '<li>inventoryTravelerIdMovements</li>'+
      '<li>inventoryTravelerIdTransforms</li>'+
      '<li>inventorySalesItemEdits</li>'+
      '<li>inventorySalesItemMovements</li>'+
      '<li>binSkuCounts</li>'+
      '<li>inventorySkuAdjustments</li>'+
      '<li>inventorySkuMovements</li>'+
      '<li>inventorySkuTransforms</li>'+
      '<li>inventoryAudit</li>'+
      '<li>singleQueryReport</li>'+
      '</ul>'
    }
  ],
});