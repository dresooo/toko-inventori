document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form[action='/api/payments']");

    if (!form) return;

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

                // Buat notifikasi ke user (misal user_id dari order)
                const orderId = formData.get("order_id");
                const userId = formData.get("user_id"); // pastikan ada di form

                await fetch("/api/notifications", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${localStorage.getItem(
                            "access_token"
                        )}`,
                    },
                    body: JSON.stringify({
                        user_id: userId, // untuk siapa notifikasi
                        type: "payment",
                        message: `Pembayaran untuk order #${orderId} berhasil.`,
                    }),
                });

                // redirect ke halaman order summary atau refresh
                window.location.href = `/orders/${formData.get("order_id")}`;
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
});
