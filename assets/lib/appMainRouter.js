'use strict';

import Marionette from 'marionette';
import AdminController from './admin/controller.js';



export default Marionette.AppRouter.extend({
  initialize(){
    new AdminController({appRouter:this});

  }
});