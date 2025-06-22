/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require("jquery");

global.$ = global.jquery = $;

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

const { scanInvoice } = require("./js/scanInvoice");

const momnet = require("moment");

// start the Stimulus application
import "./bootstrap";

//Imports Vue
import { createApp } from "vue";
import { createPinia } from "pinia";
//VueComponents
import Stock from "./components/stock/Index.vue";
import Proveedor from "./components/proveedores/Index.vue";
import Ventas from "./components/ventas/Index.vue";
import Personal from "./components/personal/Index.vue";
import Facturas from "./components/facturas/Index.vue";
import Ajustes from "./components/ajustes/Index.vue";
import Tienda from "./components/tienda/Index.vue";
import sComida from "./components/statscomida/Index.vue";
import sBebida from "./components/statsbebida/Index.vue";
import sComensales from "./components/statscomensales/Index.vue";
import Fichaje from "./components/fichaje/Index.vue";
import Tfacturas from "./components/tickettofactura/Index.vue";

import Cocina from "./components/cocina/Index.vue";
import Papelera from "./components/papelera/Index.vue";

import Caja from "./components/caja/Index.vue";
//Imports Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { aliases, mdi } from "vuetify/iconsets/mdi";
//Imports Vue3-easy-data-table
import Vue3EasyDataTable from "vue3-easy-data-table";
import "vue3-easy-data-table/dist/style.css";

const Swal = window.Swal2;

//Import de fontawesome
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.min.js";
import "@mdi/font/css/materialdesignicons.css";

//Luxon trabaja con fechas necesario para tabulator
import { DateTime } from "luxon";
const now = DateTime.local();

const pinia = createPinia();

//Vuetify
const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: "light",
    themes: {
      darkBlue: {
        dark: true,
        variables: {}, // ✅ this property is required to avoid Vuetify crash
      },
    },
  },
  icons: {
    defaultSet: "mdi",
    aliases,
    sets: {
      mdi,
    },
  },
});

//Vue init
//Stock
const app = createApp(Stock);
app.use(vuetify);
app.use(pinia);
app.component("EasyDataTable", Vue3EasyDataTable);
app.mount("#app");
//Caja
const appCaja = createApp(Caja);
appCaja.use(vuetify);
appCaja.component("EasyDataTable", Vue3EasyDataTable);
appCaja.mount("#appCaja");
//tienda
const appTienda = createApp(Tienda);
appTienda.use(vuetify);
appTienda.use(pinia);
appTienda.component("EasyDataTable", Vue3EasyDataTable);
appTienda.mount("#appTienda");
//statsComida
const appStatsComida = createApp(sComida);
appStatsComida.use(vuetify);
appStatsComida.use(pinia);
appStatsComida.component("EasyDataTable", Vue3EasyDataTable);
appStatsComida.mount("#appStatsComida");
//statsBebida
const appStatsBebida = createApp(sBebida);
appStatsBebida.use(vuetify);
appStatsBebida.use(pinia);
appStatsBebida.component("EasyDataTable", Vue3EasyDataTable);
appStatsBebida.mount("#appStatsBebida");
//statsComensales
const appStatsComensales = createApp(sComensales);
appStatsComensales.use(vuetify);
appStatsComensales.use(pinia);
appStatsComensales.component("EasyDataTable", Vue3EasyDataTable);
appStatsComensales.mount("#appStatsComensales");
//Proveedores
const appProveedores = createApp(Proveedor);
appProveedores.use(vuetify);
appProveedores.use(pinia);
appProveedores.component("EasyDataTable", Vue3EasyDataTable);
appProveedores.mount("#appProveedores");
//Ventas
const appVentas = createApp(Ventas);
appVentas.use(vuetify);
appVentas.use(pinia);
appVentas.component("EasyDataTable", Vue3EasyDataTable);
appVentas.mount("#appVentas");
//Personal
const appPersonal = createApp(Personal);
appPersonal.use(vuetify);
appPersonal.use(pinia);
appPersonal.component("EasyDataTable", Vue3EasyDataTable);
appPersonal.mount("#appPersonal");
//Facturas
const appFacturas = createApp(Facturas);
appFacturas.use(vuetify);
appFacturas.use(pinia);
appFacturas.component("EasyDataTable", Vue3EasyDataTable);
appFacturas.mount("#appFacturas");
//Ajustes
const appAjustes = createApp(Ajustes);
appAjustes.use(vuetify);
appAjustes.use(pinia);
appAjustes.component("EasyDataTable", Vue3EasyDataTable);
appAjustes.mount("#appAjustes");
//Cocina
const appCocina = createApp(Cocina);
appCocina.use(vuetify);
appCocina.use(pinia);
appCocina.component("EasyDataTable", Vue3EasyDataTable);
appCocina.mount("#appCocina");
//Papelera
const appPapelera = createApp(Papelera);
appPapelera.use(vuetify);
appPapelera.use(pinia);
appPapelera.component("EasyDataTable", Vue3EasyDataTable);
appPapelera.mount("#appPapelera");
//Fichaje
const appFichaje = createApp(Fichaje);
appFichaje.use(vuetify);
appFichaje.use(pinia);
appFichaje.component("EasyDataTable", Vue3EasyDataTable);
appFichaje.mount("#appFichaje");
//Fichaje
const appTFacturas = createApp(Tfacturas);
appTFacturas.use(vuetify);
appTFacturas.use(pinia);
appTFacturas.component("EasyDataTable", Vue3EasyDataTable);
appTFacturas.mount("#appTfacturas");

//Cargar PDF y estraer datos en JSON

// document.getElementById("fileInput").addEventListener("input", function () {
//   scanInvoice();
// });
