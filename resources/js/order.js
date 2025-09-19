// ============================
// Quantity Control
// ============================
const quantityInput = document.getElementById("quantityInput");
const decreaseBtn = document.getElementById("decreaseQty");
const increaseBtn = document.getElementById("increaseQty");

function updateQuantity(change) {
    let qty = parseInt(quantityInput.value) || 1;
    qty = Math.max(1, qty + change);
    quantityInput.value = qty;
    hitungSubtotal();
}

if (decreaseBtn && increaseBtn && quantityInput) {
    decreaseBtn.addEventListener("click", () => updateQuantity(-1));
    increaseBtn.addEventListener("click", () => updateQuantity(1));
    quantityInput.addEventListener("input", hitungSubtotal);
}

// ============================
// Alamat (API wilayah Indonesia)
// ============================
const provinsiEl = document.getElementById("provinsi");
const kotaEl = document.getElementById("kota");
const kecamatanEl = document.getElementById("kecamatan");
const kelurahanEl = document.getElementById("kelurahan");

async function fetchData(url, targetEl, placeholder) {
    if (!targetEl) return;
    targetEl.innerHTML = "";
    const placeholderOption = document.createElement("option");
    placeholderOption.value = "";
    placeholderOption.textContent = placeholder;
    targetEl.appendChild(placeholderOption);

    const res = await fetch(url);
    const data = await res.json();
    data.forEach((item) => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = item.name;
        targetEl.appendChild(option);
    });
}

if (provinsiEl) {
    fetchData(
        "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json",
        provinsiEl,
        "Pilih Provinsi"
    );

    provinsiEl.addEventListener("change", () => {
        fetchData(
            `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinsiEl.value}.json`,
            kotaEl,
            "Pilih Kota/Kabupaten"
        );
        kecamatanEl.innerHTML = `<option value="">Pilih Kecamatan</option>`;
        kelurahanEl.innerHTML = `<option value="">Pilih Kelurahan</option>`;
    });

    kotaEl.addEventListener("change", () => {
        fetchData(
            `https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kotaEl.value}.json`,
            kecamatanEl,
            "Pilih Kecamatan"
        );
        kelurahanEl.innerHTML = `<option value="">Pilih Kelurahan</option>`;
    });

    kecamatanEl.addEventListener("change", () => {
        fetchData(
            `https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecamatanEl.value}.json`,
            kelurahanEl,
            "Pilih Kelurahan"
        );
    });
}

// ============================
// Hitung Subtotal
// ============================
const hargaProdukEl = document.getElementById("hargaProduk");
const subtotalEl = document.getElementById("subtotal");
const adminEl = document.getElementById("biaya-admin");
const pengirimanEl = document.getElementById("biaya-pengiriman");

function hitungSubtotal() {
    if (!hargaProdukEl || !quantityInput || !subtotalEl) return;

    const hargaProduk = parseInt(hargaProdukEl.dataset.harga) || 0;
    const qty = parseInt(quantityInput.value) || 1;
    const biayaAdmin = 5000;
    const biayaPengiriman = 20000;

    const subtotalProduk = hargaProduk * qty;
    const total = subtotalProduk + biayaAdmin + biayaPengiriman;

    if (adminEl)
        adminEl.textContent = "Rp " + biayaAdmin.toLocaleString("id-ID");
    if (pengirimanEl)
        pengirimanEl.textContent =
            "Rp " + biayaPengiriman.toLocaleString("id-ID");
    subtotalEl.textContent = "Rp " + total.toLocaleString("id-ID");
}

document.addEventListener("DOMContentLoaded", hitungSubtotal);

// ============================
// Handle Order Submit
// ============================
document.addEventListener("DOMContentLoaded", () => {
    const orderForm = document.getElementById("orderForm");
    if (!orderForm) return;

    orderForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Ambil user & token dari localStorage
        const userId = localStorage.getItem("user_id");
        const token = localStorage.getItem("access_token");
        if (!userId || !token) {
            alert("Silakan login terlebih dahulu!");
            return;
        }

        // Ambil product_id dari URL: /order/{id}
        const urlParts = window.location.pathname.split("/");
        const productId = urlParts[urlParts.length - 1];

        const quantity = parseInt(quantityInput.value) || 1;

        // Alamat
        const provinsi = provinsiEl?.selectedOptions[0]?.text || "";
        const kota = kotaEl?.selectedOptions[0]?.text || "";
        const kecamatan = kecamatanEl?.selectedOptions[0]?.text || "";
        const kelurahan = kelurahanEl?.selectedOptions[0]?.text || "";
        const kodePos =
            document.querySelector('input[name="kode_pos"]')?.value || "";
        const shippingAddr = `${kelurahan}, ${kecamatan}, ${kota}, ${provinsi}, ${kodePos}`;

        // Total amount
        const hargaProduk = parseInt(hargaProdukEl.dataset.harga) || 0;
        const biayaAdmin = 5000;
        const biayaPengiriman = 20000;
        const totalAmount =
            hargaProduk * quantity + biayaAdmin + biayaPengiriman;

        // FormData
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("product_id", productId);
        formData.append("quantity", quantity);
        formData.append("total_amount", totalAmount);
        formData.append("shipping_addr", shippingAddr);

        const customFile = document.querySelector('input[name="custom_gambar"]')
            ?.files[0];
        if (customFile) formData.append("custom_gambar", customFile);

        try {
            const res = await fetch("http://127.0.0.1:8000/api/orders", {
                method: "POST",
                body: formData,
                headers: {
                    Accept: "application/json",
                    Authorization: `Bearer ${token}`,
                },
            });
            const data = await res.json();
            if (res.ok) {
                alert("Order berhasil dibuat!");
                orderForm.reset();
                hitungSubtotal();
            } else {
                console.error(data);
                alert(
                    "Gagal membuat order: " + (data.message || "Cek console")
                );
            }
        } catch (error) {
            console.error(error);
            alert("Terjadi error saat mengirim order");
        }
    });
});
