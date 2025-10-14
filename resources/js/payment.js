document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       FORM PEMBAYARAN
    =========================== */
    const form = document.querySelector("form[action='/api/payments']");
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        )?.content,
                    },
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);

                    // Buat notifikasi ke user
                    const orderId = formData.get("order_id");
                    const userId = formData.get("user_id");

                    await fetch("/api/notifications", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${getAccessToken()}`,
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            type: "payment",
                            message: `Pembayaran untuk order #${orderId} berhasil.`,
                        }),
                    });

                    // Redirect ke halaman order summary
                    window.location.href = `/orders/${formData.get(
                        "order_id"
                    )}`;
                } else {
                    alert(
                        "Gagal membuat payment: " +
                            (result.message || "Unknown error")
                    );
                }
            } catch (err) {
                console.error("Error submit payment:", err);
                alert("Terjadi kesalahan saat mengirim payment.");
            }
        });
    }
});

/* ===========================
   FUNGSI KONFIRMASI BATAL
=========================== */
window.confirmCancel = async function (orderId) {
    try {
        const res = await fetch(`/api/orders/${orderId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Authorization: `Bearer ${getAccessToken()}`,
            },
        });

        const result = await res.json();

        if (result.success) {
            alert(result.message);
            window.location.href = "/homepage";
        } else {
            alert(
                "Gagal membatalkan order: " +
                    (result.message || "Unknown error")
            );
        }
    } catch (err) {
        console.error("Error delete order:", err);
        alert("Terjadi kesalahan saat membatalkan order.");
    }
};

// Helper function untuk get token (sesuaikan dengan cara penyimpanan token di aplikasi Anda)
function getAccessToken() {
    // Ganti dengan metode penyimpanan token Anda
    // Bisa dari cookie, session, atau variabel global
    return document.querySelector('meta[name="api-token"]')?.content || "";
}
