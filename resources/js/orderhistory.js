document.addEventListener("DOMContentLoaded", () => {
    const userId = localStorage.getItem("user_id");
    const orderHistoryList = document.getElementById("orderHistoryList");
    const showAllBtn = document.getElementById("showAllOrdersBtn");

    // 🟩 Fungsi terjemahkan status
    function translateStatus(status) {
        const map = {
            awaiting_payment: "Menunggu Pembayaran",
            pending: "Menunggu Konfirmasi",
            paid: "Sudah Dibayar",
            processing: "Sedang Diproses",
            shipped: "Sedang Dikirim",
            delivered: "Sudah Diterima",
            cancelled: "Dibatalkan",
            verified: "Terverifikasi",
            rejected: "Ditolak",
        };
        return map[status] || status;
    }

    // 🟦 Fungsi untuk memberi warna badge status dengan style lebih modern
    function getStatusBadge(status) {
        const badgeStyles = {
            awaiting_payment:
                "bg-gradient-to-r from-yellow-400 to-orange-400 text-white shadow-sm",
            pending:
                "bg-gradient-to-r from-amber-400 to-yellow-500 text-white shadow-sm",
            paid: "bg-gradient-to-r from-green-400 to-emerald-500 text-white shadow-sm",
            processing:
                "bg-gradient-to-r from-blue-400 to-indigo-500 text-white shadow-sm",
            shipped:
                "bg-gradient-to-r from-indigo-400 to-purple-500 text-white shadow-sm",
            delivered:
                "bg-gradient-to-r from-green-500 to-teal-500 text-white shadow-sm",
            cancelled:
                "bg-gradient-to-r from-red-400 to-rose-500 text-white shadow-sm",
            verified:
                "bg-gradient-to-r from-emerald-500 to-green-600 text-white shadow-sm",
            rejected:
                "bg-gradient-to-r from-red-500 to-pink-600 text-white shadow-sm",
        };
        return (
            badgeStyles[status] ||
            "bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-sm"
        );
    }

    // 🎨 Icon untuk setiap status
    function getStatusIcon(status) {
        const icons = {
            awaiting_payment: "⏳",
            pending: "⏰",
            paid: "✅",
            processing: "⚙️",
            shipped: "🚚",
            delivered: "📦",
            cancelled: "❌",
            verified: "✓",
            rejected: "⛔",
        };
        return icons[status] || "📋";
    }

    async function loadOrderHistory(limit = 3) {
        try {
            const res = await fetch(`/api/orders/history/${userId}`, {
                headers: {
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem(
                        "access_token"
                    )}`,
                },
            });

            const data = await res.json();
            orderHistoryList.innerHTML = "";

            // Update total order
            const totalOrdersElem = document.getElementById("totalOrders");
            const totalOrders = data.data ? data.data.length : 0;
            totalOrdersElem.textContent = totalOrders;

            if (!data.data || totalOrders === 0) {
                orderHistoryList.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-6xl mb-3">📭</div>
                    <p class='text-gray-400 text-sm'>Belum ada riwayat order</p>
                </div>
            `;
                showAllBtn.style.display = "none";
                return;
            }

            const limitedOrders = data.data.slice(0, limit);
            limitedOrders.forEach((order) => {
                const translated = translateStatus(order.status);
                const badgeClass = getStatusBadge(order.status);
                const icon = getStatusIcon(order.status);

                const item = document.createElement("div");
                item.className =
                    "group relative p-4 rounded-xl hover:shadow-lg cursor-pointer border border-gray-200 mb-3 transition-all duration-300 hover:border-blue-300 hover:-translate-y-0.5 bg-white";

                item.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                        ${icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 mb-2 truncate group-hover:text-blue-600 transition-colors">
                            ${order.product.nama}
                        </p>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold ${badgeClass} px-3 py-1 rounded-full">
                                ${translated}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>${new Date(order.created_at).toLocaleString(
                                "id-ID",
                                {
                                    day: "numeric",
                                    month: "short",
                                    year: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                }
                            )}</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            `;

                item.addEventListener("click", () => {
                    window.location.href = `/orderhistory/${order.order_id}`;
                });

                orderHistoryList.appendChild(item);
            });

            showAllBtn.style.display = totalOrders > 3 ? "block" : "none";
        } catch (err) {
            console.error("Error saat memuat riwayat order:", err);
            orderHistoryList.innerHTML = `
            <div class="text-center py-8">
                <div class="text-6xl mb-3">⚠️</div>
                <p class='text-gray-400 text-sm'>Gagal memuat riwayat order</p>
            </div>
        `;
        }
    }

    let isShowingAll = false;

    // initial load (3 terakhir)
    loadOrderHistory(3);

    // event toggle tampilkan semua / show less
    showAllBtn.addEventListener("click", () => {
        if (isShowingAll) {
            loadOrderHistory(3);
            showAllBtn.textContent = "Tampilkan Semua";
            isShowingAll = false;
        } else {
            loadOrderHistory(100);
            showAllBtn.textContent = "Tampilkan Lebih Sedikit";
            isShowingAll = true;
        }
    });
});
