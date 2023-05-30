import AdminInterface from './components/AdminInterface.vue';
import Calendar from './components/Calendar.vue';

const routes = [
  {
    path: '/',
    component: Calendar,
  },
  {
    path: '/admin',
    component: AdminInterface,
  },
];

export default routes;
