console.log('APP.JS CARREGOU');

import "./bootstrap";

/* =======================
   Highcharts (Vite-safe)
======================= */
import Highcharts from "highcharts";

import "highcharts/highcharts-more";
import "highcharts/highcharts-3d";
import "highcharts/modules/solid-gauge";

window.Highcharts = Highcharts;


/* =======================
   Lucide Icons
======================= */
import { createIcons, icons } from "lucide";

document.addEventListener("DOMContentLoaded", () => {
    createIcons({ icons });
});

/* =======================
   Alpine.js
======================= */
import Alpine from "alpinejs";
import costsTable from "./costsTable";

window.Alpine = Alpine;

Alpine.data('costsTable', costsTable);

Alpine.start();


/* =======================
   Charts (safe import)
======================= */
try {
    await import("./charts/auditoria");
} catch (e) {
    console.warn("auditoria.js não carregou:", e);
}
