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
// Handle Order Submit (diperbarui)
// ============================
document.addEventListener("DOMContentLoaded", () => {
    const orderForm = document.getElementById("orderForm");
    if (!orderForm) return;

    orderForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const userId = localStorage.getItem("user_id");
        const token = localStorage.getItem("access_token");
        if (!userId || !token) {
            alert("Silakan login terlebih dahulu!");
            return;
        }

        const urlParts = window.location.pathname.split("/");
        const productId = urlParts[urlParts.length - 1];
        const quantity = parseInt(quantityInput.value) || 1;

        // ===== Ambil data nama & no telepon =====
        const fullName =
            document.querySelector('input[name="full_name"]')?.value || "";
        const phoneNumber =
            document.querySelector('input[name="phone_number"]')?.value || "";

        // ===== Ambil data alamat =====
        const provinsi = provinsiEl?.selectedOptions[0]?.text || "";
        const kota = kotaEl?.selectedOptions[0]?.text || "";
        const kecamatan = kecamatanEl?.selectedOptions[0]?.text || "";
        const kelurahan = kelurahanEl?.selectedOptions[0]?.text || "";
        const kodePos =
            document.querySelector('input[name="kode_pos"]')?.value || "";
        const alamatLengkap =
            document.querySelector('textarea[name="shipping_addr"]')?.value ||
            "";

        // Gabungkan alamat final
        const shippingAddr = `${alamatLengkap}, ${kelurahan}, ${kecamatan}, ${kota}, ${provinsi}, ${kodePos}`;

        // ===== Hitung total =====
        const hargaProduk = parseInt(hargaProdukEl.dataset.harga) || 0;
        const biayaAdmin = 5000;
        const biayaPengiriman = 20000;
        const totalAmount =
            hargaProduk * quantity + biayaAdmin + biayaPengiriman;

        // ===== Kirim ke API =====
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("product_id", productId);
        formData.append("quantity", quantity);
        formData.append("total_amount", totalAmount);
        formData.append("full_name", fullName);
        formData.append("phone_number", phoneNumber);
        formData.append("shipping_addr", shippingAddr);

        // Upload file / canvas
        if (pinCanvas) {
            const canvasDataUrl = await exportCanvasWithShape();
            const canvasFile = dataURLtoFile(
                canvasDataUrl,
                "custom_gambar.png"
            );
            formData.append("custom_gambar", canvasFile);
        }

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

                // ✅ Redirect ke halaman payment
                const orderId = data.order?.order_id;
                const paymentUrl = data.payment_url || `/payment/${orderId}`;
                window.location.href = paymentUrl;
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

async function exportCanvasWithShape() {
    if (!pinCanvas) return null;
    pinCanvas.sendToBack(bgShape);
    // Render ulang semua objek di canvas
    pinCanvas.renderAll();

    // Export langsung hasil canvas saat ini
    return pinCanvas.toDataURL({
        format: "png",
        multiplier: 1,
        enableRetinaScaling: true,
    });
}

// ============================
// Helper: Convert DataURL → File
// ============================
function dataURLtoFile(dataurl, filename) {
    const arr = dataurl.split(",");
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
}

let pinCanvas = null;
let bgShape = null; // layer background dalam shape

document.addEventListener("DOMContentLoaded", () => {
    pinCanvas = new fabric.Canvas("pinCanvas", {
        backgroundColor: "transparent", // luar shape transparan
        selection: false,
    });

    const productType = document.getElementById("productType")?.value;
    const borderShape = addShapeByProductType(productType);

    // === semua shape (termasuk heart) punya background ===
    if (borderShape) {
        bgShape = fabric.util.object.clone(borderShape);
        bgShape.set({
            fill: "#fff", // default putih
            stroke: null,
            selectable: false,
            evented: false,
            absolutePositioned: true,
        });
        pinCanvas.add(bgShape);
        pinCanvas.sendToBack(bgShape);
    }

    // Tombol background
    document.getElementById("bgWhite")?.addEventListener("click", () => {
        if (bgShape) bgShape.set("fill", "#fff");
        pinCanvas.renderAll();
    });
    document.getElementById("bgBlack")?.addEventListener("click", () => {
        if (bgShape) bgShape.set("fill", "#000");
        pinCanvas.renderAll();
    });

    // Upload gambar
    document.getElementById("uploadImage").addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (f) {
            fabric.Image.fromURL(f.target.result, (img) => {
                img.scaleToWidth(250);
                img.scaleToHeight(250);
                img.set({ left: 25, top: 25 });

                if (borderShape) {
                    const clip = fabric.util.object.clone(borderShape);
                    clip.set({
                        absolutePositioned: true,
                        evented: false,
                        fill: null,
                        stroke: null,
                    });
                    img.clipPath = clip;
                }

                pinCanvas.add(img).setActiveObject(img);
                pinCanvas.renderAll();
            });
        };
        reader.readAsDataURL(file);
    });
});

function addShapeByProductType(productType) {
    let shape;
    const shapeStyle = {
        fill: "transparent", // hanya border
        stroke: "#000",
        strokeWidth: 2,
        selectable: false,
        evented: false,
        shadow: {
            color: "rgba(0,0,0,0.2)",
            blur: 5,
            offsetX: 2,
            offsetY: 2,
        },
    };

    if (productType === "pin") {
        shape = new fabric.Circle({
            ...shapeStyle,
            radius: 130,
        });
    } else if (productType === "heart") {
        // Heart lebih chubby, bawah tidak terlalu lancip
        const heartPath = `
        M 310 470
        C 250 430, 180 370, 180 280
        C 180 200, 250 150, 310 200
        C 370 150, 440 200, 440 280
        C 440 370, 370 430, 310 470
        Z
    `;
        shape = new fabric.Path(heartPath, {
            ...shapeStyle,
            originX: "center",
            originY: "center",
            left: pinCanvas.width / 2,
            top: pinCanvas.height / 2,
            scaleX: 1,
            scaleY: 0.8,
        });
    } else if (productType === "square") {
        shape = new fabric.Rect({
            ...shapeStyle,
            width: 300,
            height: 300,
        });
    } else if (productType === "sticker") {
        fabric.Image.fromURL("/images/sticker.png", (img) => {
            img.set({ left: 0, top: 0, selectable: false });
            pinCanvas.add(img);

            const border = new fabric.Rect({
                left: img.left,
                top: img.top,
                width: img.width,
                height: img.height,
                fill: "transparent",
                stroke: "#000",
                strokeWidth: 2,
                selectable: false,
                shadow: {
                    color: "rgba(0,0,0,0.2)",
                    blur: 5,
                    offsetX: 2,
                    offsetY: 2,
                },
            });

            pinCanvas.add(border);
            pinCanvas.sendToBack(border);
            pinCanvas.sendToBack(img);
            pinCanvas.renderAll();
        });
        return null;
    }

    if (shape) {
        pinCanvas.add(shape);
        pinCanvas.centerObject(shape);
        pinCanvas.sendToBack(shape);
        pinCanvas.renderAll();
        return shape; // border asli
    }

    return null;
}

//tampilin ssstock product
// ============================
// Tampilkan Stock Product
// ============================
document.addEventListener("DOMContentLoaded", async () => {
    const urlParts = window.location.pathname.split("/");
    const productId = urlParts[urlParts.length - 1];
    const stockEl = document.getElementById("stokProduk");

    if (!stockEl) return; // pastikan ada elemen di HTML

    try {
        const res = await fetch("http://127.0.0.1:8000/api/stocks");
        const data = await res.json();

        const stockInfo = data.products.find((p) => p.product_id == productId);

        if (stockInfo) {
            stockEl.textContent =
                stockInfo.max_production > 0
                    ? `${stockInfo.max_production} tersedia`
                    : "Habis";
            stockEl.className =
                stockInfo.max_production > 0
                    ? "text-green-600 font-semibold"
                    : "text-red-600 font-semibold";
        } else {
            stockEl.textContent = "Tidak ada data stok";
            stockEl.className = "text-gray-500";
        }
    } catch (err) {
        console.error("Gagal load stok:", err);
        if (stockEl) stockEl.textContent = "Error memuat stok";
    }
});
