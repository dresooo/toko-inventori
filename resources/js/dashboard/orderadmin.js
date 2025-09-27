document.addEventListener("DOMContentLoaded", () => {
    fetchOrders();

    const editOrderForm = document.getElementById("editOrderForm");
    editOrderForm.addEventListener("submit", handleEditSubmit);
});

// Fetch semua order
async function fetchOrders() {
    try {
        const res = await fetch("/api/admin/orders", {
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
        });
        const data = await res.json();

        // simpan ke global variable untuk dipakai detail modal
        window.orderData = data;

        const tbody = document.getElementById("orderBody");
        tbody.innerHTML = "";

        data.forEach((order, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${order.full_name}</td>
                <td>${order.phone_number}</td>
                <td>${order.product ? order.product.nama : "-"}</td>
                <td>${order.quantity}</td>
                <td>Rp ${parseInt(order.total_amount).toLocaleString(
                    "id-ID"
                )}</td>
                <td>${new Date(order.order_date).toLocaleDateString(
                    "id-ID"
                )}</td>
                <td>
                    <span class="badge ${
                        order.status === "processing"
                            ? "badge-warning"
                            : order.status === "paid"
                            ? "badge-success"
                            : order.status === "cancelled"
                            ? "badge-error"
                            : "badge-neutral"
                    }">${order.status}</span>
                </td>
                <td>
                    <button onclick="window.openDetailModal(${order.order_id})"
                        class="px-3 py-1 rounded-lg bg-blue-500 text-white hover:bg-blue-600 shadow-md">
                        Lihat Detail
                    </button>
                    <button onclick="window.openEditOrderModal(${
                        order.order_id
                    }, '${order.status}')"
                        class="px-3 py-1 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 shadow-md">
                        Edit Status
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Gagal load order:", err);
    }
}

// Buka modal edit order
window.openEditOrderModal = function (orderId, currentStatus) {
    document.getElementById("editOrderId").value = orderId;
    document.getElementById("editOrderStatus").value = currentStatus;
    document.getElementById("editOrderModal").showModal();
};

// Tutup modal edit
window.closeEditModal = function () {
    document.getElementById("editOrderModal").close();
};

// Submit form edit
async function handleEditSubmit(e) {
    e.preventDefault();
    const orderId = document.getElementById("editOrderId").value;
    const status = document.getElementById("editOrderStatus").value;

    const spinner = document.getElementById("editOrderSpinner");
    spinner.classList.remove("hidden");

    try {
        const res = await fetch(`/api/admin/orders/${orderId}/status`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
            body: JSON.stringify({ status }),
        });

        const result = await res.json();
        console.log("Update:", result);

        spinner.classList.add("hidden");
        closeEditModal();
        fetchOrders(); // refresh tabel
    } catch (err) {
        spinner.classList.add("hidden");
        console.error("Gagal update status:", err);
    }
}

// Show detail order menggunakan data lokal
window.openDetailModal = function (orderId) {
    const order = window.orderData.find((o) => o.order_id === orderId);

    if (!order) {
        console.error("Order tidak ditemukan:", orderId);
        return;
    }

    document.getElementById("detailOrderId").innerText = order.order_id;
    document.getElementById("detailName").innerText = order.full_name;
    document.getElementById("detailPhone").innerText = order.phone_number;
    document.getElementById("detailProduct").innerText = order.product
        ? order.product.nama
        : "-";
    document.getElementById("detailQty").innerText = order.quantity;
    document.getElementById("detailTotal").innerText =
        "Rp " + parseInt(order.total_amount).toLocaleString("id-ID");
    document.getElementById("detailDate").innerText = new Date(
        order.order_date
    ).toLocaleString("id-ID");
    document.getElementById("detailStatus").innerText = order.status;
    document.getElementById("detailAddress").innerText = order.shipping_addr;
    document.getElementById("detailEmail").innerText = order.user?.email || "-";

    // buka modal detail
    document.getElementById("orderDetailModal").classList.remove("hidden");
};

window.closeDetailModal = function () {
    document.getElementById("orderDetailModal").classList.add("hidden");
};
