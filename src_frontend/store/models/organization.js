import { Model } from '@vuex-orm/core';

export default class Organization extends Model {
  static entity = 'organization'

  static fields () {
    return {
      id: this.number(null),
      name: this.string(null)
    };
  }
};
