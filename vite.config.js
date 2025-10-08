import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "./product.js",
                "./login.js",
                "./register.js",
                "./profil.js",
                "./logout.js",
                "./order.js",
                "./productstock.js",
                "./payment.js",
                "./dashboard/productadmin.js",
                "./dashboard/rawmaterial.js",
                "./dashboard/productrecipe.js",
                "./dashboard/stock.js",
                "./dashboard/orderadmin.js",
                "./dashboard/notification.js",
            ],
            refresh: true,
        }),
    ],
});
