import BaseApi from './base.js';

export default class MyselfApi extends BaseApi {
  getMyself () {
    return this.sendHttpRequest({
      url: '/myself',
      method: 'GET'
    });
  }
  syncProfile (profileData) {
    return this.sendHttpRequest({
      url: '/profile',
      method: 'PUT',
      body: JSON.stringify(profileData)
    });
  }
  login (loginInfo) {
    return this.sendHttpRequest({
      url: '/login_check',
      method: 'POST',
      body: JSON.stringify(loginInfo)
    });
  }
}
