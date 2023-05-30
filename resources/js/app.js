import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'tailwindcss/tailwind.css'



// Import components
import App from './components/App.vue';
import routes from './routes';

const router = createRouter({
  history: createWebHistory(),
  routes,
});

const app = createApp(App);
app.use(router);
app.mount('#app');


