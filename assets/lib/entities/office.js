define(["lib/app"], function(StepThrough){
  StepThrough.module("Entities", function(Entities, StepThrough, Backbone, Marionette, $, _){
    var  officeChannel = Backbone.Radio.channel('office');
    Entities.Office = Backbone.Model.extend({});

    Entities.OfficeCollection = Backbone.Collection.extend({
      model: Entities.Office,
      url: StepThrough.baseURL+'/office',
      parse: function(response) {
        return response.list;
      }
    });

    var initializeOffices = function(){
      Entities.offices = new Entities.OfficeCollection();
      Entities.offices.on('sync', function(offices){
        officeChannel.trigger("entities:offices:initialized", offices);
      });
      Entities.offices.fetch();
    };

    var API = {
      getOffices: function(){
        if(Entities.offices === undefined){
          initializeOffices();
        }
        return Entities.offices;
      }
    };

    officeChannel.comply("entities:offices:initialize", function(){
      API.getOffices();
    });

  });

  return ;
});