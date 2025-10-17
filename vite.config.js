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
                "resources/js/payment.js",
                "resources/js/orderhistory.js",
                "resources/js/orderhistorydetail.js",
                "resources/js/dashboard/productadmin.js",
                "resources/js/dashboard/rawmaterial.js",
                "resources/js/dashboard/productrecipe.js",
                "resources/js/dashboard/stock.js",
                "resources/js/dashboard/orderadmin.js",
                "resources/js/dashboard/notification.js",
                "resources/js/dashboard/dashboard.js",
            ],
            refresh: true,
        }),
    ],
});
