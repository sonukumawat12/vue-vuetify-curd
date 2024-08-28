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
import Dashboard from './components/dashboard/index.vue';
import imageUpload from './components/img.vue';

// Create Vue App Instance
const app = createApp({});

// Register Components
app.component('example-component', ExampleComponent);
app.component('user-component', User);
app.component('dashboard-component', Dashboard);
app.component('image-upload', imageUpload);

// Use Plugins
app.use(vuetify);
app.use(Vue3Toastify);

// Mounting App
app.mount("#app");
