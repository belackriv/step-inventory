
const debug = process.env.NODE_ENV !== 'production';

class AuthenticationError extends Error {
  constructor (msg) {
    super(msg);
    this.isAuthenticationError = true;
  }
}

export default class BaseApi {
  getBaseFetchOptions () {
    const headers = new Headers({
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    });
    return {
      headers: headers,
      credentials: 'same-origin',
      method: 'GET'
    };
  }
  handleErrors (response) {
    if (!response.ok) {
      if (response.status === 401) {
        throw new AuthenticationError(response.statusText);
      } else {
        throw Error(response.statusText);
      }
    }
    return response;
  }
  sendHttpRequest (options) {
    return new Promise((resolve, reject) => {
      const defaults = this.getBaseFetchOptions();
      options = {...defaults, ...options};
      let url = debug ? '/api' + options.url : options.url;
      fetch(url, options)
        .then(this.handleErrors)
        .then((response) => {
          response.json().then((responseData) => {
            resolve(responseData);
          });
        }).catch((error) => {
          if (error.isAuthenticationError) {
            reject(error);
          } else {
            console.log(error);
          }
        });
    });
  }
}
