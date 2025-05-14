import AltGoogle2FAStatus from './Components/2fa-status.vue';

Statamic.booting(() => {
    Statamic.$components.register('alt_google_2fa_status-fieldtype', AltGoogle2FAStatus);
});
