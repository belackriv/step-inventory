define(["lib/app", "lib/apps/admin/users/list_view"], function(StepThrough, View){
  StepThrough.module("AdminApp.Users", function(Users, StepThrough, Backbone, Marionette, $, _){
    var  userChannel = Backbone.Radio.channel('user');
    Users.ListController = {
      listUsers: function(criteria){
//"entities/role"        
        require(["lib/common/views", "lib/entities/user" ], function(CommonViews){
          var loadingView = new CommonViews.Loading();
          StepThrough.AdminApp.appLayout.getRegion('content').show(loadingView);

          var fetchingUsers = userChannel.request("entities:users:fetch", criteria);

          $.when(fetchingUsers).done(function(users){
            var listView = new View.ListView({
              collection:users
            });
            StepThrough.AdminApp.appLayout.getRegion('content').show(listView);
          });
/*
          var contactsListLayout = new View.Layout();
          var contactsListPanel = new View.Panel();

          require(["entities/common"], function(FilteredCollection){
            $.when(fetchingContacts).done(function(contacts){
              var filteredContacts = StepThrough.Entities.FilteredCollection({
                collection: contacts,
                filterFunction: function(filterCriterion){
                  var criterion = filterCriterion.toLowerCase();
                  return function(contact){
                    if(contact.get('firstName').toLowerCase().indexOf(criterion) !== -1
                      || contact.get('lastName').toLowerCase().indexOf(criterion) !== -1
                      || contact.get('phoneNumber').toLowerCase().indexOf(criterion) !== -1){
                        return contact;
                    }
                  };
                }
              });

              if(criterion){
                filteredContacts.filter(criterion);
                contactsListPanel.once("show", function(){
                  contactsListPanel.triggerMethod("set:filter:criterion", criterion);
                });
              }

              var contactsListView = new View.Contacts({
                collection: filteredContacts
              });

              contactsListPanel.on("contacts:filter", function(filterCriterion){
                filteredContacts.filter(filterCriterion);
                StepThrough.trigger("contacts:filter", filterCriterion);
              });

              contactsListLayout.on("show", function(){
                contactsListLayout.panelRegion.show(contactsListPanel);
                contactsListLayout.contactsRegion.show(contactsListView);
              });

              contactsListPanel.on("contact:new", function(){
                require(["apps/contacts/new/new_view"], function(NewView){
                  var newContact = StepThrough.request("contact:entity:new");

                  var view = new NewView.Contact({
                    model: newContact
                  });

                  view.on("form:submit", function(data){
                    if(contacts.length > 0){
                      var highestId = contacts.max(function(c){ return c.id; }).get("id");
                      data.id = highestId + 1;
                    }
                    else{
                      data.id = 1;
                    }
                    if(newContact.save(data)){
                      contacts.add(newContact);
                      view.trigger("dialog:close");
                      var newContactView = contactsListView.children.findByModel(newContact);
                      // check whether the new contact view is displayed (it could be
                      // invisible due to the current filter criterion)
                      if(newContactView){
                        newContactView.flash("success");
                      }
                    }
                    else{
                      view.triggerMethod("form:data:invalid", newContact.validationError);
                    }
                  });

                  StepThrough.dialogRegion.show(view);
                });
              });

              contactsListView.on("childview:contact:show", function(childView, args){
                StepThrough.trigger("contact:show", args.model.get("id"));
              });

              contactsListView.on("childview:contact:edit", function(childView, args){
                require(["apps/contacts/edit/edit_view"], function(EditView){
                  var model = args.model;
                  var view = new EditView.Contact({
                    model: model
                  });

                  view.on("form:submit", function(data){
                    if(model.save(data)){
                      childView.render();
                      view.trigger("dialog:close");
                      childView.flash("success");
                    }
                    else{
                      view.triggerMethod("form:data:invalid", model.validationError);
                    }
                  });

                  StepThrough.dialogRegion.show(view);
                });
              });

              contactsListView.on("childview:contact:delete", function(childView, args){
                args.model.destroy();
              });

              StepThrough.mainRegion.show(contactsListLayout);
            });
          });
*/
        });
      }
    }
  });

  return StepThrough.AdminApp.Users.ListController;
});