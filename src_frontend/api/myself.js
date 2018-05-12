import BaseApi from './base.js';

export default class MyselfApi extends BaseApi {
  getMyself (cb) {
    this.sendHttpRequest({
      url: '/myself',
      method: 'GET'
    }).then((myself) => {
      cb(myself);
    });
  }
}
