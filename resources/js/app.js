import {createApp} from 'vue'

import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'toastr/build/toastr.min.css';

import App from './App.vue'

let app = createApp(App)

app.mount("#app")
