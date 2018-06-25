'use strict';

import _ from 'underscore';
import Handlebars from 'handlebars/handlebars.runtime.js';
import Moment from 'moment';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

import TravelerIdModel from 'lib/inventory/models/travelerIdModel.js';
import SalesItemModel from 'lib/inventory/models/salesItemModel.js';
import BinSkuCountModel from 'lib/inventory/models/binSkuCountModel.js';
import UnitTypePropertyModel from 'lib/inventory/models/unitTypePropertyModel.js';

const castAsType = function(value, type){
  switch (type) {
    case 'integer':
      return parseInt(value);
    case 'boolean':
      let boolValue = value?true:false;
      return boolValue;
    case 'string':
      return ''+value;
    default:
      return value;
  }
};

const lpad = function(str, width, fill){
  fill = fill || '0';
  width = width || 6;
  str = str + '';
  return str.length >= width ? str : new Array(width - str.length + 1).join(fill) + str;
};

Handlebars.registerHelper('tableCell', function(column, data, options){
  if( typeof column != "object" ||
    typeof data != "object"
  ){return;}
  let type = column.type.toLowerCase().replace(' ','');
  let value = castAsType(data[column.name], type);
  if(typeof Handlebars.helpers[column.helper] === 'function'){
    let helperOptions = _.extend(options, column.helperOptions);
    return Handlebars.helpers[column.helper](value, helperOptions);
  }
  switch (type) {
    case 'integer':
      return new Handlebars.SafeString(parseInt(value));
    case 'boolean':
      return Handlebars.helpers.boolean(value, options);
    case 'percent':
      return Handlebars.helpers.percent(value, options);
    case 'datetime':
       return Handlebars.helpers.moment(value, options);
    default:
      return new Handlebars.SafeString(value);
  }
});

Handlebars.registerHelper('castAsType', function(value, type, options){
  if(value.date){
    type = 'datetime';
    value = Moment.parseZone(value.date+' '+value.timezone);
  }
  let castedValue = castAsType(value, type);
  switch (type) {
    case 'integer':
      return new Handlebars.SafeString(parseInt(castedValue));
    case 'boolean':
      return Handlebars.helpers.boolean(castedValue, options);
    case 'percent':
      return Handlebars.helpers.percent(castedValue, options);
    case 'datetime':
       return Handlebars.helpers.moment(castedValue, options);
    default:
      return new Handlebars.SafeString(castedValue);
  }
});

Handlebars.registerHelper('statusCode', function(data, options){
  return data?'Enabled':'Disabled';
});

Handlebars.registerHelper('percent', function(data, options){
  let value = Math.round(parseFloat(data) * 10000)/100;
  return isNaN(value)?'':value+'%';
});

Handlebars.registerHelper('titleCase', function(str, options){
  var newstr = (str+'').replace('_', ' ').split(' ');
  for(var i=0;i<newstr.length;i++){
    var copy = newstr[i].substring(1).toLowerCase();
    newstr[i] = newstr[i][0].toUpperCase() + copy;
  }
   newstr = newstr.join(" ");
   return newstr;
});

Handlebars.registerHelper('upperCase', function(str, options){
   return (str+'').toUpperCase();
});

Handlebars.registerHelper('boolean', function(boolean, options){
   return (boolean)?'Yes':'No';
});

Handlebars.registerHelper('moment', function(data, options){
  var format = options.hash.format?options.hash.format:'h:mm A, ddd MMM D YYYY';
  return Moment(data).format(format);
});

Handlebars.registerHelper('log', function(data, options){
  console.log(data);
  return '';
});

Handlebars.registerHelper('ifIsRouteActive', function(route, options){
  route = (route[0]=='/')?route.slice(1):route;
  let routeRegExp = new RegExp('^'+route+'(/\\d+)?$');
  let currentRoute = Radio.channel('app').request('currentRoute');
  if(routeRegExp.test(currentRoute)){
    return options.fn(this);
  } else {
    return options.inverse(this);
  }
});

Handlebars.registerHelper('isGrantedRole', function(role, options){
  let myself =  Radio.channel('data').request('myself');
  return (myself.isGrantedRole(role, myself) )?options.fn(this):options.inverse(this);
});

Handlebars.registerHelper('isMyself', function (user, options){
  let myself =  Radio.channel('data').request('myself');
  return (myself.id === user.id)?options.fn(this):options.inverse(this);
});

Handlebars.registerHelper('concat', function(options){
  let str = '';
  _.each(arguments, (arg)=>{
    if(typeof arg === 'string'){
      str += arg;
    }
  });
  return str;
});

Handlebars.registerHelper('lpad', function(data, options){
  return lpad(data, options.width, options.z)
});

Handlebars.registerHelper('truncate', function(data, options){
  let maxLength = options.hash.maxLength?parseInt(options.hash.maxLength):30;
  let str = ''+data;
  if(str.length > maxLength && maxLength > 3){
    return str.substr(0, maxLength - 3)+'...';
  }else{
    return str.substr(0, maxLength);
  }
});

Handlebars.registerHelper('translate', function(term, dict, options){
  return dict[term];
});

Handlebars.registerHelper('join', function(array, seperator, options){
  if(typeof array === 'object' && array && array.join){
    seperator = (typeof seperator==='string')?seperator:',';
    return array.join(seperator);
  }else{
    return '';
  }
});

Handlebars.registerHelper('return', function(fn, context){
  let args = Array.from(arguments);
  args = args.slice(2);
  return (typeof fn === 'function')?fn.apply(context, args):null;
});

Handlebars.registerHelper('barcodeHtml', function(data, options){
  let barcodeValue = '';
  if(data instanceof TravelerIdModel || options.hash.type == 'TravelerIdModel'){
    barcodeValue = data.attributes?data.attributes.label:data.label;
  }else if(data instanceof SalesItemModel || options.hash.type == 'SalesItemModel'){
    barcodeValue = data.attributes?data.attributes.label:data.label;
  }else if(data instanceof BinSkuCountModel || options.hash.type == 'BinSkuCountModel'){
    barcodeValue = data.attributes?data.attributes.sku.attributes.label:data.sku.attributes.label;
  }else{
    throw 'Must supply a Model or a option Type to barcodeHtml helper';
  }
  if(data instanceof TravelerIdModel || data instanceof SalesItemModel){
    data = data.attributes;
  }
  let html;
  if(options.hash.isCard){
    html = '<div data-ui-top-label><p class="has-text-centered">';
  }else{
    html = '<p class="has-text-centered" data-ui-top-label>';
  }
  if(data.sku){
    if(data.sku.attributes.unit){
      html += 'Unit: '+data.sku.attributes.unit.attributes.serial;
    }else if(data.sku.attributes.part){
      html += 'Part: '+data.sku.attributes.part.attributes.name;
    }else if(data.sku.attributes.commodity){
      html += 'Item: '+data.sku.attributes.commodity.attributes.name;
    }else if(data.sku.attributes.unitType){
      html += 'Type: '+data.sku.attributes.unitType.attributes.name;
    }
  }
  html += '</p>';
  if(options.hash.isCard){
    html += '</div>';
  }
  let svgClass = '';
  if(data.isVoid){
    svgClass = 'si-is-void';
  }
  if(data.transform && !data.transform.attributes.isVoid){
    svgClass += ' si-has-transform';
  }
  html += '<svg jsbarcode-value="'+barcodeValue+'" class="'+svgClass+'"></svg>';
  return new Handlebars.SafeString(html);
});



Handlebars.registerHelper('getAspectRatioClass', function(width, height, options){
  let ratio = width/height;
  if(ratio < 1.3){
    return 'is-1by1';
  }else if(ratio < 1.5){
    return 'is-4by3';
  }else if(ratio < 1.7){
    return 'is-3by2';
  }else if(ratio < 2.0){
    return 'is-16by9';
  }else if(ratio < 2.5){
    return 'is-2by1';
  }else{
    return '';
  }
});


Handlebars.registerHelper('baseUrl', function(options){
   let str = BaseUrlBaseModel.prototype.baseUrl+'';
  _.each(arguments, (arg)=>{
    if(typeof arg === 'string'){
      str += arg;
    }
  });
  return str;
});

Handlebars.registerHelper('ifCond', function(v1, operator, v2, options){
  switch (operator) {
    case '==':
      return (v1 == v2) ? options.fn(this) : options.inverse(this);
    case '===':
      return (v1 === v2) ? options.fn(this) : options.inverse(this);
    case '<':
      return (v1 < v2) ? options.fn(this) : options.inverse(this);
    case '<=':
      return (v1 <= v2) ? options.fn(this) : options.inverse(this);
    case '>':
      return (v1 > v2) ? options.fn(this) : options.inverse(this);
    case '>=':
      return (v1 >= v2) ? options.fn(this) : options.inverse(this);
    case '&&':
      return (v1 && v2) ? options.fn(this) : options.inverse(this);
    case '||':
      return (v1 || v2) ? options.fn(this) : options.inverse(this);
    default:
      return options.inverse(this);
  }
});

Handlebars.registerHelper('renderUnitTypePropertyTypesOptions', function(options){
  let html = '';
  _.each(UnitTypePropertyModel.prototype.types, (label, value)=>{
    let selected = '';
    if(this.propertyType === value){
      selected = 'selected';
    }
    html += '<option '+selected+' value="'+value+'">'+label+'</option>';
  });
  return new Handlebars.SafeString(html);
});

const getUnitPropertyInputHtml = function(data, options){
  let html = '';
  let value = null;
  let unitPropertyIdData = '';
  if(options.hash.useUnitPropertyId){
    unitPropertyIdData = 'data-unit-property-id="'+data.id+'"';
  }
  if(data.unitTypeProperty.get('validValues').length > 0){
    html = '<select '+unitPropertyIdData+' data-unit-type-property-id="'+data.unitTypeProperty.id+'" '+
      'data-value-name="integerValue" class="select">';
    data.unitTypeProperty.get('validValues').each((validValue)=>{
      let selected = (data.integerValue == validValue.id)?'selected':'';
      html += '<option '+selected+' value="'+validValue.id+'">'+validValue.get(data.unitTypeProperty.get('propertyType')+'Value')+'</option>';
    })
    html += '</select>';
  }else{
    if(data.unitTypeProperty.get('propertyType') === UnitTypePropertyModel.prototype.TYPE_INTEGER){
      value = (data.integerValue === null)?'':data.integerValue+'';
      html = '<input '+unitPropertyIdData+' data-unit-type-property-id="'+data.unitTypeProperty.id+'" data-value-name="integerValue" type="number" value="'+value+'" step="1"/>';
    }else if(data.unitTypeProperty.get('propertyType') === UnitTypePropertyModel.prototype.TYPE_FLOAT){
      value = (data.floatValue === null)?'':data.floatValue+'';
      html = '<input '+unitPropertyIdData+' data-unit-type-property-id="'+data.unitTypeProperty.id+'" data-value-name="floatValue" type="number" value="'+value+'" step=".01" />';
    }else if(data.unitTypeProperty.get('propertyType') === UnitTypePropertyModel.prototype.TYPE_BOOLEAN){
      html = '<select '+unitPropertyIdData+' data-unit-type-property-id="'+data.unitTypeProperty.id+'" data-value-name="booleanValue" class="select">';
      if(data.booleanValue){
        html += '<option value="0">False</option><option selected value="1">True</option>';
      }else{
        html += '<option selected value="0">False</option><option value="1">True</option>';
      }
      html += '</select>';
    }else{
      value = (data.stringValue === null)?'':data.stringValue+'';
      html = '<input '+unitPropertyIdData+' data-unit-type-property-id="'+data.unitTypeProperty.id+'" data-value-name="stringValue" type="text" value="'+value+'" />';
    }
  }
  return html;
}

Handlebars.registerHelper('renderUnitTypePropertyValidValueInput', function(data, options){
  return new Handlebars.SafeString(getUnitPropertyInputHtml(data, options));
});

Handlebars.registerHelper('renderUnitPropertyInput', function(data, options){
  return new Handlebars.SafeString(getUnitPropertyInputHtml(data, options));
});
