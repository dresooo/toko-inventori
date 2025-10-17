import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/product.js",
                "resources/login.js",
                "resources/register.js",
                "resources/profil.js",
                "resources/logout.js",
                "resources/order.js",
                "resources/productstock.js",
                "resources/payment.js",
                "resources/orderhistory.js",
                "resources/orderhistorydetail.js",
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
