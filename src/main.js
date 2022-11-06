import {createApp} from 'vue';
import {createPinia} from 'pinia';
import Oruga from '@oruga-ui/oruga-next'
import './style.css';
import '@oruga-ui/oruga-next/dist/oruga-full.css'
import App from './App.vue';

const pinia = createPinia();

createApp(App).use(pinia).use(Oruga).mount('#app');
