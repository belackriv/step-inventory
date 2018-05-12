
const debug = process.env.NODE_ENV !== 'production';

export default class BaseApi {
  getBaseFetchOptions () {
    const headers = new Headers({
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });
    return {
      headers: headers,
      credentials: 'omit',
      method: 'GET'
    };
  }
  sendHttpRequest (options) {
    return new Promise((resolve, reject) => {
      const defaults = this.getBaseFetchOptions();
      options = {...defaults, ...options};
      let url = debug ? '/api' + options.url : options.url;
      fetch(url, options).then((response) => {
        if (response.ok) {
          response.json().then((responseData) => {
            resolve(responseData);
          });
        } else {
          this.getErrorMessageFromResponse(response).then((errorMsg) => { reject(errorMsg); });
        }
      });
    });
  }
}
