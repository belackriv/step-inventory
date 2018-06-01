import { Database } from '@vuex-orm/core';

import myself from './modules/myself.js';
import Myself from './models/myself.js';
import organization from './modules/organization.js';
import Organization from './models/organization.js';

const database = new Database();

database.register(Myself, myself);
database.register(Organization, organization);

export default database;
