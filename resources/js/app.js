import './bootstrap';
import { createApp } from 'vue';
import vuetify from "./vuetify";
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import '@mdi/font/css/materialdesignicons.css';

// Vue App Instances
const app = createApp({});
const user = createApp({});

// Components Import
import ExampleComponent from './components/ExampleComponent.vue';
import User from './components/User.vue';

// Registering Components
app.component('example-component', ExampleComponent);
user.component('users', User);

// Using Plugins and Mounting
app.use(vuetify).mount("#app");
user.use(vuetify).use(Vue3Toastify).mount("#userData");
