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
