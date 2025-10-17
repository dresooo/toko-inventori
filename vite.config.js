import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/product.js",
                "resources/js/login.js",
                "resources/js/register.js",
                "resources/js/profil.js",
                "resources/js/logout.js",
                "resources/js/order.js",
                "resources/js/productstock.js",
                "resources/js/payment.js",
                "resources/js/orderhistory.js",
                "resources/js/orderhistorydetail.js",
                "./dashboard/productadmin.js",
                "./dashboard/rawmaterial.js",
                "./dashboard/productrecipe.js",
                "./dashboard/stock.js",
                "./dashboard/orderadmin.js",
                "./dashboard/notification.js",
                "./dashboard/dashboard.js",
            ],
            refresh: true,
        }),
    ],
});
