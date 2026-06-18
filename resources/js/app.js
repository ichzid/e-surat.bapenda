import '../css/app.css';
import './bootstrap';
import 'vue3-toastify/dist/index.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Vue3Toastify from 'vue3-toastify';

const appName = import.meta.env.VITE_APP_NAME || 'E-Surat';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Vue3Toastify, {
                autoClose: 3000,
                position: 'top-right',
                theme: 'light', // Ubah ke light agar lebih soft
                hideProgressBar: true, // Sembunyikan progress bar agar lebih elegan
                transition: 'bounce',
                toastStyle: {
                    borderRadius: '12px',
                    boxShadow: '0 4px 16px 0 rgba(15, 23, 42, 0.07)',
                    border: '1px solid #E2E8F0',
                    fontFamily: 'DM Sans, sans-serif',
                    fontSize: '14px',
                    fontWeight: '500'
                }
            })
            .mount(el);
    },
    progress: {
        color: '#059669', // Menggunakan warna sage dari tema kita
    },
});
