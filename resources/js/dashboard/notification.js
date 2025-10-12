document.addEventListener("DOMContentLoaded", async () => {
    // 🔹 Saat pertama kali load, ambil notifikasi baru
    fetchNotifications(false);
});

// 🔹 Tandai semua notifikasi sebagai sudah dibaca
async function markAllAsRead() {
    try {
        const res = await fetch("/api/admin/notifications/mark-all-read", {
            method: "PUT",
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
        });
        if (!res.ok)
            console.warn("Gagal menandai semua notifikasi sebagai dibaca");
    } catch (err) {
        console.error("Error markAllAsRead:", err);
    }
}

// 🔹 Ambil notifikasi
async function fetchNotifications(showAll = false) {
    try {
        const res = await fetch("/api/admin/notifications", {
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
        });

        if (!res.ok) {
            const errorData = await res.json();
            renderError(errorData.message || "Gagal mengambil notifikasi");
            return;
        }

        const notifications = await res.json();
        if (!Array.isArray(notifications)) {
            renderError("Format data tidak valid");
            return;
        }

        // 🔹 Tampilkan dulu notifikasi baru
        renderNotifications(notifications, showAll);

        // 🔹 Setelah tampil (dan hanya kalau bukan showAll), tandai sebagai read
        if (!showAll) {
            await markAllAsRead();
        }
    } catch (err) {
        console.error("Gagal ambil notifikasi:", err);
        renderError("Terjadi kesalahan saat mengambil notifikasi");
    }
}

// 🔹 Tampilkan pesan error
function renderError(message) {
    const container = document.querySelector("#notification-list");
    if (container) {
        container.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <p class="text-red-600 font-semibold">${message}</p>
            </div>
        `;
    }
}

// 🔹 Render notifikasi ke halaman
function renderNotifications(data, showAll = false) {
    const container = document.querySelector("#notification-list");
    if (!container) {
        console.error("Element #notification-list tidak ditemukan");
        return;
    }

    container.innerHTML = "";

    if (data.length === 0) {
        container.innerHTML = `<p class="text-gray-500 text-center">Tidak ada notifikasi baru.</p>`;
        return;
    }

    // Urutkan berdasarkan tanggal terbaru
    const sortedData = data.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
    );

    // Pisahkan notifikasi baru & lama
    const newNotifs = sortedData.filter((n) => n.is_read === 0);
    const oldNotifs = sortedData.filter((n) => n.is_read === 1);

    // Jika tidak showAll, tampilkan hanya notifikasi baru
    const visibleNotifs = showAll ? sortedData : newNotifs;

    // Kalau kosong dan bukan showAll
    if (visibleNotifs.length === 0 && !showAll) {
        container.innerHTML = `<p class="text-gray-500 text-center">Tidak ada notifikasi baru.</p>`;
    }

    visibleNotifs.forEach((n) => {
        const card = document.createElement("div");
        card.className =
            "flex items-center justify-between bg-white shadow-sm rounded-2xl p-4 mb-3 border border-gray-200 hover:shadow-md transition-all duration-200";

        card.innerHTML = `
            <div class="flex flex-col">
                <p class="font-medium text-gray-800">${n.message}</p>
                <p class="text-xs text-gray-500 mt-1">${new Date(
                    n.created_at
                ).toLocaleString()}</p>
            </div>
            <div class="flex gap-2">
                <button 
                    onclick="deleteNotification(${n.notification_id})"
                    class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-2.5 py-1 rounded-lg transition-all duration-150 active:scale-95 shadow-sm">
                    Hapus
                </button>
            </div>
        `;
        container.appendChild(card);
    });

    // 🔹 Tombol tampilkan riwayat lama
    if (!showAll && oldNotifs.length > 0) {
        const showAllBtn = document.createElement("button");
        showAllBtn.textContent = "Tampilkan Semua Riwayat";
        showAllBtn.className =
            "mt-3 text-sm font-medium text-blue-600 hover:underline text-center w-full";
        showAllBtn.onclick = () => fetchNotifications(true);
        container.appendChild(showAllBtn);
    }

    // 🔹 Tombol kembali ke notifikasi baru
    if (showAll) {
        const backBtn = document.createElement("button");
        backBtn.textContent = "Kembali ke Notifikasi Baru";
        backBtn.className =
            "mt-3 text-sm font-medium text-gray-600 hover:underline text-center w-full";
        backBtn.onclick = () => fetchNotifications(false);
        container.appendChild(backBtn);
    }
}

// 🔹 Hapus notifikasi
window.deleteNotification = async function (id) {
    if (!confirm("Yakin hapus notifikasi ini?")) return;
    try {
        const res = await fetch(`/api/admin/notifications/${id}`, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
        });

        if (!res.ok) {
            const errorData = await res.json();
            alert(
                "Gagal menghapus notifikasi: " + (errorData.message || "Error")
            );
            return;
        }

        fetchNotifications(false);
    } catch (err) {
        console.error("Error deleteNotification:", err);
        alert("Terjadi kesalahan");
    }
};
