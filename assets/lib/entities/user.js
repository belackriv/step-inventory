define(["lib/app","lib/stepthrough_wamp_pusher"], function(StepThrough, StepThroughWampPusher){
  StepThrough.module("Entities", function(Entities, StepThrough, Backbone, Marionette, $, _){
    var pusher = new StepThroughWampPusher('com.stepthrough.user');
    var  userChannel = Backbone.Radio.channel('user');
    Entities.User = Backbone.Model.extend({
      initialize: function(){
        pusher.addModel(this);
      }
    });

    Entities.UserCollection = Backbone.Collection.extend({
      model: Entities.User,
      url: StepThrough.baseURL+'/user',
      parse: function(response) {
        return response.list;
      }
    });



    var API = {
      getUsers: function(criteria){
        var users = new Entities.UserCollection();
        var defer = $.Deferred();
        users.fetch({
          success: function(data){
            defer.resolve(data);
          },
          data: criteria
        });
        return defer.promise();
      }
    };

    userChannel.reply("entities:users:fetch", function(criteria){
      return API.getUsers(criteria);
    });
  });

  return;
});