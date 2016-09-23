'use strict';

import _ from 'underscore';
import Handlebars from 'handlebars/handlebars.runtime.js';
import Moment from 'moment';
import Radio from 'backbone.radio';
import BaseUrlBaseModel from 'lib/common/models/baseUrlBaseModel.js';

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

Handlebars.registerHelper('tableCell', function(column, data, options) {
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

Handlebars.registerHelper('boolean', function(data, options) {
  return data?'True':'False';
});

Handlebars.registerHelper('statusCode', function(data, options) {
  return data?'Enabled':'Disabled';
});

Handlebars.registerHelper('percent', function(data, options) {
  let value = Math.round(parseFloat(data) * 10000)/100;
  return isNaN(value)?'':value+'%';
});

Handlebars.registerHelper('titleCase', function(str, options) {
  var newstr = (str+'').split(" ");
  for(var i=0;i<newstr.length;i++){
    var copy = newstr[i].substring(1).toLowerCase();
    newstr[i] = newstr[i][0].toUpperCase() + copy;
  }
   newstr = newstr.join(" ");
   return newstr;
});

Handlebars.registerHelper('upperCase', function(str, options) {
   return (str+'').toUpperCase();
});

Handlebars.registerHelper('boolean', function(boolean, options) {
   return (boolean)?'Yes':'No';
});

Handlebars.registerHelper('moment', function(data, options) {
  var format = options.hash.format?options.hash.format:'h:mm A, ddd MMM D YYYY';
  return Moment(data).format(format);
});


Handlebars.registerHelper('log', function(data, options) {
  console.log(data);
  return '';
});

Handlebars.registerHelper('ifIsRouteActive', function(route, options) {
  route = (route[0]=='/')?route.slice(1):route;
  let routeRegExp = new RegExp('^'+route+'(/\\d+)?$');
  let currentRoute = Radio.channel('app').request('currentRoute');
  if(routeRegExp.test(currentRoute)){
    return options.fn(this);
  } else {
    return options.inverse(this);
  }
});

Handlebars.registerHelper('isGrantedRole', function (role, options) {
  let myself =  Radio.channel('data').request('myself');
  return (myself.isGrantedRole(role, myself) )?options.fn(this):options.inverse(this);
});

Handlebars.registerHelper('isMyself', function (user, options) {
  let myself =  Radio.channel('data').request('myself');
  return (myself.id === user.id)?options.fn(this):options.inverse(this);
});

Handlebars.registerHelper('concat', function(options) {
  let str = '';
  _.each(arguments, (arg)=>{
    if(typeof arg === 'string'){
      str += arg;
    }
  });
  return str;
});

Handlebars.registerHelper('truncate', function(data, options) {
  let maxLength = options.hash.maxLength?parseInt(options.hash.maxLength):30;
  let str = ''+data;
  if(str.length > maxLength && maxLength > 3){
    return str.substr(0, maxLength - 3)+'...';
  }else{
    return str.substr(0, maxLength);
  }
});

Handlebars.registerHelper('translate', function(term, dict, options) {
  return dict[term];
});

Handlebars.registerHelper('join', function(array, seperator, options) {
  if(typeof array === 'object' && array && array.join){
    seperator = (typeof seperator==='string')?seperator:',';
    return array.join(seperator);
  }else{
    return '';
  }
});

Handlebars.registerHelper('return', function(fn, context) {
  let args = Array.from(arguments);
  args = args.slice(2);
  return (typeof fn === 'function')?fn.apply(context, args):null;
});


Handlebars.registerHelper('baseUrl', function (options) {
   let str = BaseUrlBaseModel.prototype.baseUrl+'';
  _.each(arguments, (arg)=>{
    if(typeof arg === 'string'){
      str += arg;
    }
  });
  return str;
});

Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
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