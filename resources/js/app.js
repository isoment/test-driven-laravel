import './bootstrap';

import { createApp } from 'vue';

import Welcome from './components/Welcome.vue';
import TicketCheckout from  './components/TicketCheckout.vue';

const app = createApp({});

app.component('welcome', Welcome);
app.component('ticket-checkout', TicketCheckout);

app.mount('#app');