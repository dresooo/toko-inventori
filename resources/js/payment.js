document.addEventListener("DOMContentLoaded", () => {
    console.log("✅ DOM Loaded, mulai observasi...");

    const observer = new MutationObserver(() => {
        const form = document.querySelector("#paymentForm");
        if (form) {
            console.log("✅ Form ditemukan, event listener ditambahkan.");
            observer.disconnect();

            form.addEventListener("submit", async (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                // Validasi bank
                if (!formData.get("bank")) {
                    showError("Pilih metode pembayaran terlebih dahulu!");
                    return;
                }

                // Validasi file payment_proof
                const file = formData.get("payment_proof");
                console.log("File object:", file);
                console.log("File type:", file.type, "File size:", file.size);
                if (!file || file.size === 0) {
                    showError("Upload bukti pembayaran terlebih dahulu!");
                    return;
                }
                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/jpg",
                    "application/pdf",
                ];
                if (!allowedTypes.includes(file.type)) {
                    showError("File harus berupa jpg, jpeg, png, atau pdf!");
                    return;
                }

                // Tombol submit
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Processing...";
                }

                // DEBUG: tampilkan semua field
                for (let pair of formData.entries()) {
                    console.log(pair[0], pair[1]);
                }

                try {
                    const actionUrl = "/payments/web";
                    console.log("📤 Mengirim request ke:", actionUrl);
                    const response = await fetch(actionUrl, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            )?.content,
                            Accept: "application/json",
                        },
                        credentials: "same-origin", // wajib
                    });

                    // parsing JSON
                    const result = await response.json();

                    if (result.success) {
                        if (window.Swal) {
                            await Swal.fire({
                                icon: "success",
                                title: "Pembayaran Berhasil!",
                                text: result.message,
                                confirmButtonText: "OK",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then(() => {
                                window.location.href = "/homepage"; // redirect setelah klik OK
                            });
                        } else {
                            alert(result.message);
                            window.location.href = "/homepage";
                        }
                    } else {
                        showError(
                            result.message ||
                                "Terjadi kesalahan saat memproses pembayaran."
                        );
                    }
                } catch (err) {
                    console.error("❌ Error submit payment:", err);
                    showError("Terjadi kesalahan saat mengirim payment.");
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = "Kirim Pembayaran";
                    }
                }
            });
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });

    function showError(msg) {
        if (window.Swal) {
            Swal.fire({ icon: "error", title: "Kesalahan!", text: msg });
        } else {
            alert(msg);
        }
    }
});

// Fungsi batal order (Tidak ada perubahan yang diperlukan pada fungsi ini)
window.confirmCancel = async function (orderId) {
    const confirmed = window.Swal
        ? await Swal.fire({
              title: "Yakin Batalkan Pesanan?",
              text: "Tindakan ini tidak dapat dibatalkan.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Ya, Batalkan",
              cancelButtonText: "Batal",
          })
        : confirm("Apakah Anda yakin ingin membatalkan order ini?");

    if (window.Swal && !confirmed.isConfirmed) return;
    if (!window.Swal && !confirmed) return;

    try {
        console.log("🗑️ Menghapus order:", orderId);

        const res = await fetch(`/orders/${orderId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
                Accept: "application/json",
            },
            credentials: "same-origin",
        });

        const result = await res.json();

        if (res.ok && result.success) {
            if (window.Swal) {
                await Swal.fire({
                    icon: "success",
                    title: "Pesanan Dibatalkan",
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
            } else {
                alert(result.message);
            }
            window.location.href = "/homepage";
        } else {
            showError(result.message || "Gagal membatalkan order");
        }
    } catch (err) {
        console.error("❌ Error delete order:", err);
        showError("Terjadi kesalahan saat membatalkan order.");
    }

    function showError(msg) {
        if (window.Swal) {
            Swal.fire({ icon: "error", title: "Kesalahan!", text: msg });
        } else {
            alert(msg);
        }
    }
};
