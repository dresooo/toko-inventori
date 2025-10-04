document.addEventListener("DOMContentLoaded", () => {
    fetchOrders();

    const editOrderForm = document.getElementById("editOrderForm");
    editOrderForm.addEventListener("submit", handleEditSubmit);
});

// Fetch semua order
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

        window.orderData = data; // simpan ke global untuk detail modal

        const tbody = document.getElementById("orderBody");
        tbody.innerHTML = "";

        data.forEach((order, index) => {
            const orderStatus = order.status?.trim().toLowerCase();

            // Tentukan badge & displayStatus
            let badgeClass = "badge-neutral";
            let displayStatus = orderStatus;

            if (orderStatus === "awaiting_payment") {
                badgeClass = "badge-warning";
                displayStatus = "AWAITING_PAYMENT";
            } else if (orderStatus === "processing") {
                badgeClass = "badge-info";
                displayStatus = "PENDING_PAYMENT";
            } else if (orderStatus === "paid") {
                badgeClass = "badge-success";
                displayStatus = "PAID";
            } else if (orderStatus === "shipped") {
                badgeClass = "badge-info";
                displayStatus = "SHIPPED";
            } else if (orderStatus === "cancelled") {
                badgeClass = "badge-error";
                displayStatus = "REJECTED";
            }

            // Tombol aksi dengan flex
            let actionButtons = `
                <div class="flex flex-wrap gap-2">
                    <button onclick="window.openDetailModal(${order.order_id})"
                        class="px-3 py-1 rounded-lg bg-blue-500 text-white hover:bg-blue-600 shadow-md min-w-[80px]">
                        Lihat Detail
                    </button>
            `;

            if (orderStatus === "awaiting_payment") {
                actionButtons += `<button disabled class="px-3 py-1 rounded-lg bg-gray-400 text-white cursor-not-allowed min-w-[80px]">Edit Status</button>`;
            } else if (orderStatus === "processing") {
                actionButtons += `<button onclick="window.openEditOrderModal(${order.order_id}, '${order.status}')"
                    class="px-3 py-1 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 shadow-md min-w-[80px]">Edit Status</button>`;
            } else if (orderStatus === "paid") {
                actionButtons += `<button onclick="window.updateOrderStatus(${order.order_id}, 'shipped')"
                    class="px-3 py-1 rounded-lg bg-green-500 text-white hover:bg-green-600 shadow-md min-w-[80px]">Kirim (Shipped)</button>`;
            } else if (orderStatus === "shipped") {
                actionButtons += `<span class="text-green-700 font-bold">Completed</span>`;
            } else if (orderStatus === "cancelled") {
                actionButtons += `<span class="text-red-600 font-bold">Cancelled</span>`;
            }

            actionButtons += `</div>`; // tutup div flex

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
                <td><span class="badge  p-3 text-white ${badgeClass}">${displayStatus}</span></td>
                <td>${actionButtons}</td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Gagal load order:", err);
    }
}

// API call update status langsung (shipped / verified / rejected)
window.updateOrderStatus = async function (orderId, action) {
    if (!confirm(`Yakin ingin ubah status menjadi ${action}?`)) return;

    try {
        const res = await fetch(`/api/admin/orders/${orderId}/status`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
            body: JSON.stringify({ action }),
        });

        const result = await res.json();
        console.log("Update:", result);

        fetchOrders(); // refresh tabel
    } catch (err) {
        console.error("Gagal update status:", err);
    }
};

// Modal edit status (verified / rejected)
window.openEditOrderModal = function (orderId, currentStatus) {
    document.getElementById("editOrderId").value = orderId;

    const statusSelect = document.getElementById("editOrderStatus");
    if (statusSelect) {
        statusSelect.value = "verified"; // default
    }

    document.getElementById("editOrderModal").showModal();
};

window.closeEditModal = function () {
    document.getElementById("editOrderModal").close();
};

// Submit form edit (verified / rejected)
async function handleEditSubmit(e) {
    e.preventDefault();
    const orderId = document.getElementById("editOrderId").value;
    const action = document.getElementById("editOrderStatus").value;

    const spinner = document.getElementById("editOrderSpinner");
    spinner.classList.remove("hidden");

    try {
        const res = await fetch(`/api/admin/orders/${orderId}/status`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("access_token")}`,
            },
            body: JSON.stringify({ action }),
        });

        const result = await res.json();
        console.log("Update:", result);

        spinner.classList.add("hidden");
        closeEditModal();
        fetchOrders();
    } catch (err) {
        spinner.classList.add("hidden");
        console.error("Gagal update status:", err);
    }
}

// Detail modal
// Detail modal
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

    // ===========================
    // Custom Gambar (upload user)
    // ===========================
    console.log("🎨 Custom gambar dari order:", order.custom_gambar);
    const imgEl = document.getElementById("detailProductImage");
    const downloadCustomBtn = document.getElementById("downloadCustomImageBtn");

    if (order.custom_gambar) {
        let customPath = order.custom_gambar;

        // Bersihkan path
        customPath = customPath.replace(/^(storage\/|public\/)/g, "");
        if (!customPath.startsWith("uploads/")) {
            customPath = "uploads/" + customPath;
        }

        const customImageUrl = `/storage/${customPath}`;
        imgEl.src = customImageUrl;

        console.log("✅ Custom image path:", customImageUrl);

        if (downloadCustomBtn) {
            downloadCustomBtn.classList.remove("hidden");
            downloadCustomBtn.onclick = () => {
                const link = document.createElement("a");
                link.href = customImageUrl;
                link.download = `custom_design_order_${order.order_id}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };
        }

        imgEl.onerror = function () {
            console.warn("⚠️ Custom image tidak ditemukan:", this.src);
            if (this.src.includes("/storage/")) {
                const altPath = this.src.replace("/storage/", "/");
                this.onerror = () => {
                    console.error("❌ Custom image gagal dimuat");
                    this.onerror = null;
                    this.src = "/no-image.png";
                };
                this.src = altPath;
            } else {
                this.onerror = null;
                this.src = "/no-image.png";
            }
        };
    } else {
        imgEl.src = "https://via.placeholder.com/150?text=No+Custom+Image";
        if (downloadCustomBtn) {
            downloadCustomBtn.classList.add("hidden");
        }
        console.log("ℹ️ Tidak ada custom gambar");
    }

    const paymentProofContainer = document.getElementById(
        "paymentProofContainer"
    );
    const paymentProofImg = document.getElementById("detailPaymentProof");
    const downloadPaymentProofBtn = document.getElementById(
        "downloadPaymentProofBtn"
    );

    // Cek apakah payment ada dan ada payment_proof
    if (order.payment && order.payment.payment_proof) {
        const proofBase64 = order.payment.payment_proof;

        // Tentukan MIME type (biasanya PNG, bisa dicek jika JPEG)
        let mimeType = "image/png";
        if (proofBase64.startsWith("/9j/")) {
            mimeType = "image/jpeg";
        }

        const paymentProofUrl = `data:${mimeType};base64,${proofBase64}`;

        paymentProofImg.src = paymentProofUrl;
        paymentProofContainer.classList.remove("hidden");

        downloadPaymentProofBtn.classList.remove("hidden");
        downloadPaymentProofBtn.onclick = () => {
            const link = document.createElement("a");
            link.href = paymentProofUrl;
            link.download = `payment_proof_order_${order.order_id}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
    } else {
        paymentProofContainer.classList.add("hidden");
        downloadPaymentProofBtn.classList.add("hidden");
        console.log("ℹ️ Tidak ada bukti pembayaran");
    }

    // ===========================
    // Tombol Download SKU
    // ===========================
    document.getElementById("downloadSkuBtn").onclick = () =>
        downloadSku(order);

    document.getElementById("orderDetailModal").classList.remove("hidden");
};

window.closeDetailModal = function () {
    document.getElementById("orderDetailModal").classList.add("hidden");
};

async function downloadSku(order) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Judul
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("DETAIL ORDER", 105, 20, { align: "center" });

    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);

    // Info order
    let y = 40;
    const lineHeight = 8;

    doc.text(`Order ID   : ${order.order_id}`, 20, y);
    y += lineHeight;
    doc.text(`Nama       : ${order.full_name}`, 20, y);
    y += lineHeight;
    doc.text(`No. Telp   : ${order.phone_number}`, 20, y);
    y += lineHeight;
    doc.text(`Email        : ${order.user?.email || "-"}`, 20, y);
    y += lineHeight;
    doc.text("Alamat     :", 20, y);
    y += lineHeight;

    // Biar alamat panjang otomatis wrap
    let splitAddress = doc.splitTextToSize(order.shipping_addr || "-", 170);
    doc.text(splitAddress, 20, y);
    y += splitAddress.length * lineHeight;

    y += 6;
    doc.setFont("helvetica", "bold");
    doc.text("=== PRODUK ===", 20, y);
    doc.setFont("helvetica", "normal");
    y += lineHeight;

    doc.text(`Nama Produk: ${order.product ? order.product.nama : "-"}`, 20, y);
    y += lineHeight;
    doc.text(`Jumlah     : ${order.quantity}`, 20, y);
    y += lineHeight;
    y += lineHeight;
    // Simpan PDF
    doc.save(`SKU-${order.order_id}.pdf`);
}
