import './bootstrap';
import { createApp } from 'vue';
import vuetify from "./vuetify";
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import '@mdi/font/css/materialdesignicons.css';
import 'cropperjs/dist/cropper.css';

// Components Import
import ExampleComponent from './components/ExampleComponent.vue';
import User from './components/user.vue';
import Dash from './components/dashboard/index.vue'


// Create Vue App Instance
const app = createApp({});
const dash = createApp({});

// Register Components
app.component('example-component', ExampleComponent);
app.component('user-component', User);
dash.component('dashboard-component', Dash);


// Use Plugins
app.use(vuetify);
app.use(Vue3Toastify);

// Mounting App
app.mount("#app");
dash.mount("#dash");
