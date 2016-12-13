'use strict';

import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import ProfileView from 'lib/common/views/profileView.js';

export default Marionette.Object.extend({
  profile(){
    Radio.channel('app').trigger('show:view', new ProfileView());
  },
});