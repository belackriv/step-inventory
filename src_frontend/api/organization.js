import BaseApi from './base.js';

export default class MyselfApi extends BaseApi {
  getOrganization (id) {
    return this.sendHttpRequest({
      url: '/organization/' + id,
      method: 'GET'
    });
  }
}
