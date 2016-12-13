'use strict';

import globalNamespace from 'lib/globalNamespace.js';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

let Model = BaseUrlBaseModel.extend({
  initialize(){
    this.set('behavoirs', []);
  },
  behavoirList: {
    'cannotHaveParent': 'Can not have parent',
  },
  urlRoot(){
    return this.baseUrl+'/bin_type';
  },
  defaults: {
    name: null,
    description: null,
    isActive: null,
    behavoirs: null,
  },
  hasBehavoir(behavoir){
    return (this.get('behavoirs').indexOf(behavoir) > -1);
  },
  addBehavoir(behavoir){
    if(!this.hasBehavoir(behavoir)){
      this.get('behavoirs').push(behavoir);
      this.trigger('change:behavoirs', this, this.get('behavoirs'), {});
    }
  },
  removeBehavoir(behavoir){
    let index = this.get('behavoirs').indexOf(behavoir);
    if(index > -1){
      this.get('behavoirs').splice(index, 1);
      this.trigger('change:behavoirs', this, this.get('behavoirs'), {});
    }
  }
});

globalNamespace.Models.BinTypeModel = Model;

export default Model;