import { createApp } from 'vue';
import router from './router';
import AdminApp from './AdminApp.vue';
import multilingual from "@/plugins/multilingual";

const JSf_admin_app = createApp(AdminApp);

JSf_admin_app.use(router);
JSf_admin_app.use(multilingual);
JSf_admin_app.mount('#jet-smart-filters-admin-app');