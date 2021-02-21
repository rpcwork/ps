import { createApp } from "vue";
import App from "./App.vue";
import "primeflex/primeflex.css";
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import PrimeVue from "primevue/config";
import AutoComplete from "primevue/autocomplete";

const app = createApp(App);

app.use(PrimeVue, { ripple: true });

app.component("AutoComplete", AutoComplete);

app.mount("#app");
