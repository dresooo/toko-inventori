document.addEventListener("DOMContentLoaded", async () => {
    const loadingState = document.getElementById("loadingState");
    const contentContainer = document.getElementById("contentContainer");
    const errorState = document.getElementById("errorState");

    const urlParts = window.location.pathname.split("/");
    const orderId = urlParts[urlParts.length - 1];

    async function loadOrderDetail() {
        try {
            const res = await fetch(`/api/orders/detail/${orderId}`);
            if (!res.ok) throw new Error("Gagal memuat data order");

            const data = await res.json();
            const order = data.data;
            if (!order) throw new Error("Data order kosong");

            loadingState.classList.add("hidden");
            contentContainer.classList.remove("hidden");

            // === ORDER INFO ===
            document.getElementById("orderNumber").textContent = order.order_id;
            document.getElementById("orderDate").textContent = new Date(
                order.order_date
            ).toLocaleDateString("id-ID");

            // === STATUS BADGE ===
            const statusBadge = document.getElementById("statusBadge");
            const statusColors = {
                awaiting_payment: "bg-yellow-100 text-yellow-700",
                paid: "bg-green-100 text-green-700",
                processing: "bg-blue-100 text-blue-700",
                shipped: "bg-indigo-100 text-indigo-700",
                delivered: "bg-emerald-100 text-emerald-700",
                cancelled: "bg-red-100 text-red-700",
            };
            statusBadge.className = `px-4 py-2 rounded-full font-semibold text-sm ${
                statusColors[order.status] || "bg-gray-100 text-gray-600"
            }`;
            statusBadge.textContent = order.status
                .replace("_", " ")
                .toUpperCase();

            // === PRODUCT INFO ===
            // === PRODUCT INFO ===
            if (order.product) {
                document.getElementById("productName").textContent =
                    order.product.nama || "-";
                document.getElementById("productPrice").textContent =
                    "Rp" + Number(order.product.harga).toLocaleString("id-ID");
                document.getElementById("productQuantity").textContent =
                    order.quantity;

                const shippingFee = 20000;
                const adminFee = 5000;
                const productSubtotal =
                    Number(order.total_amount) - shippingFee - adminFee;

                document.getElementById("productSubtotal").textContent =
                    "Rp" + productSubtotal.toLocaleString("id-ID");
                document.getElementById("summarySubtotal").textContent =
                    "Rp" + productSubtotal.toLocaleString("id-ID");

                const productImg = document.getElementById("productImage");
                // PERBAIKAN: Gunakan gambar_base64 dari backend (bukan gambar)
                const base64 = order.product.gambar_base64;

                if (base64) {
                    // Deteksi format gambar
                    let mimeType = "image/jpeg";
                    if (base64.startsWith("UklG")) {
                        mimeType = "image/webp"; // WEBP
                    } else if (base64.startsWith("iVBOR")) {
                        mimeType = "image/png"; // PNG
                    } else if (base64.startsWith("/9j/")) {
                        mimeType = "image/jpeg"; // JPG
                    }

                    productImg.src = `data:${mimeType};base64,${base64}`;
                    productImg.alt = order.product.nama || "Product Image";
                } else {
                    productImg.src = "/assets/no-image.png";
                }
            }

            // === CUSTOMER INFO ===
            document.getElementById("customerName").textContent =
                order.full_name || "-";
            document.getElementById("customerPhone").textContent =
                order.phone_number || "-";
            document.getElementById("shippingAddress").textContent =
                order.shipping_addr || "-";

            // === RINGKASAN BIAYA ===
            const shippingFee = 20000; // hanya untuk ditampilkan
            const adminFee = 5000; // tidak ada admin fee tambahan
            const total = Number(order.total_amount); // total sudah termasuk ongkir

            document.getElementById("summaryAdmin").textContent =
                "Rp" + adminFee.toLocaleString("id-ID");
            document.getElementById("summaryShipping").textContent =
                "Rp" + shippingFee.toLocaleString("id-ID");
            document.getElementById("summaryTotal").textContent =
                "Rp" + total.toLocaleString("id-ID");
            // === CUSTOM GAMBAR ===
            if (order.custom_gambar) {
                const customContainer = document.getElementById(
                    "customImageContainer"
                );
                const customImage = document.getElementById("customImage");
                const path = order.custom_gambar;

                if (path.startsWith("uploads/")) {
                    customImage.src = `/storage/${path}`;
                } else if (path.startsWith("data:image")) {
                    customImage.src = path;
                } else {
                    customImage.src = path;
                }
                customContainer.classList.remove("hidden");
            }

            // === PAYMENT PROOF ===
            if (order.payment && order.payment.payment_proof) {
                const paymentContainer = document.getElementById(
                    "paymentProofContainer"
                );
                const paymentImage = document.getElementById("paymentProof");
                const proof = order.payment.payment_proof;

                let proofUrl = "";
                if (proof.startsWith("iVBOR") || proof.startsWith("/9j/")) {
                    const mimeType = proof.startsWith("/9j/")
                        ? "image/jpeg"
                        : "image/png";
                    proofUrl = `data:${mimeType};base64,${proof}`;
                } else if (proof.startsWith("UklG")) {
                    proofUrl = `data:image/webp;base64,${proof}`;
                } else {
                    proofUrl = proof.startsWith("uploads/")
                        ? `/storage/${proof}`
                        : proof;
                }

                paymentImage.src = proofUrl;
                paymentContainer.classList.remove("hidden");

                // Tombol download bukti pembayaran
                const downloadBtn = document.getElementById(
                    "downloadPaymentProofBtn"
                );
                if (downloadBtn) {
                    downloadBtn.classList.remove("hidden");
                    downloadBtn.onclick = () => {
                        const link = document.createElement("a");
                        link.href = proofUrl;
                        link.download = `payment_proof_order_${order.order_id}.png`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    };
                }
            }

            // === ACTION BUTTONS ===
            const actionButtons = document.getElementById("actionButtons");
            actionButtons.innerHTML = "";

            if (order.status === "awaiting_payment") {
                const payBtn = document.createElement("button");
                payBtn.textContent = "Lanjut ke Pembayaran";
                payBtn.className =
                    "w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 font-semibold";
                payBtn.onclick = () =>
                    (window.location.href = `/payment/${order.order_id}`);
                actionButtons.appendChild(payBtn);
            } else if (order.status === "shipped") {
                const confirmBtn = document.createElement("button");
                confirmBtn.textContent = "Konfirmasi Pesanan Diterima";
                confirmBtn.className =
                    "w-full bg-green-600 hover:bg-green-700 text-white rounded-lg py-2 font-semibold";
                confirmBtn.onclick = () =>
                    alert("Terima kasih sudah konfirmasi!");
                actionButtons.appendChild(confirmBtn);
            }
        } catch (error) {
            console.error("Gagal memuat detail order:", error);
            loadingState.classList.add("hidden");
            errorState.classList.remove("hidden");
        }
    }

    loadOrderDetail();
});
