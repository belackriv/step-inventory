import StepThrough from 'lib/app';
import MenuController from './menu/menu_controller';

StepThrough.module("HeaderApp", function(Header, StepThrough, Backbone, Marionette, $, _){
  var API = {
    displayHeaderMenu: function(){
      MenuController.displayHeaderMenu();
    }
  };

  Header.on("start", function(){
    API.displayHeaderMenu();
  });
});

export default StepThrough.HeaderApp;
