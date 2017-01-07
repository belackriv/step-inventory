'use strict';

import Radio from 'backbone.radio';
import Marionette from 'marionette';

import LoadingView from 'lib/common/views/loadingView.js';
import ProfileView from 'lib/common/views/profileView.js';
import AccountView from 'lib/common/views/accountView.js';
import AccountCollection from 'lib/common/models/accountCollection.js';

export default Marionette.Object.extend({
  profile(){
    Radio.channel('app').trigger('show:view', new ProfileView());
  },
  account(){
  	Radio.channel('app').trigger('show:view',  new LoadingView());
	let accounts = new AccountCollection();
    accounts.fetch().done(()=>{
        Radio.channel('app').trigger('show:view', new AccountView({
          model: accounts.first()
        }));
	});
  },
});