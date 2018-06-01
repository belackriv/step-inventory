import Vue from 'vue';
import Router from 'vue-router';
import AppMain from '@/components/AppMain';
import Nav from '@/components/Nav';
import Footer from '@/components/Footer';

import Profile from '@/components/Profile';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'AppMain',
      components: {
        Nav,
        AppMain,
        Footer
      },
      children: [
        {
          path: '/profile',
          name: 'Profile',
          components: {
            MainSection: Profile
          }
        }
      ]
    }
  ]
});
