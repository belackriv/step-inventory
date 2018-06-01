import BaseApi from './base.js';

export default class MyselfApi extends BaseApi {
  getMyself () {
    return this.sendHttpRequest({
      url: '/myself',
      method: 'GET'
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
