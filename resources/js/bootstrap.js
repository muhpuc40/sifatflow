import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/*
 * Realtime (Laravel Echo + Reverb) is OFF for the admin panel for now.
 * When you build the chat screen: set the VITE_REVERB_* values in .env,
 * then enable the next line. Without those values Echo throws an error
 * and stops every script on the page (including Alpine).
 */
// import './echo';
