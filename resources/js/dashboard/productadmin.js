async function loadProducts() {
    const grid = document.getElementById("productGrid");
    const emptyState = document.getElementById("emptyState");
    const loadingSkeleton = document.getElementById("loadingSkeleton");

    // tampilkan skeleton dulu
    grid.innerHTML = "";
    loadingSkeleton.classList.remove("hidden");
    emptyState.classList.add("hidden");

    try {
        let response = await fetch("/api/products");
        let products = await response.json();

        loadingSkeleton.classList.add("hidden");
        grid.innerHTML = "";

        if (products.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        products.forEach((product) => {
            const row = document.createElement("div");
            row.className =
                "bg-white border rounded-lg shadow-sm p-4 flex items-center justify-between hover:shadow-md transition relative";

            row.innerHTML = `
    <div class="flex items-start gap-4">
        <img src="data:image/jpeg;base64,${product.gambar}" 
            alt="${product.nama}"
            class="w-40 h-40 object-cover rounded-lg border">
        <div>
            <h3 class="font-semibold text-gray-900 ">${product.nama}</h3>
            
            <!-- ID Produk -->
            <p class="text-xs text-gray-500 mt-2">ID: ${product.product_id}</p>

            <!-- Deskripsi -->
            <p class="text-gray-600 text-sm mt-2 max-w-lg whitespace-normal break-words">
                ${product.deskripsi}
            </p>

            
        </div>
    </div>
    
    <div class="flex items-center gap-8">
        <div class="text-right">
            <p class="text-lg font-bold text-gray-900">
                Rp ${Number(product.harga).toLocaleString("id-ID")}
            </p>
            <p class="text-sm ${
                product.stok > 0 ? "text-green-600" : "text-red-600"
            }">
                ${product.stok > 0 ? product.stok + " tersedia" : "Habis"}
            </p>
        </div>
        <div class="flex gap-2">
            <button 
                onclick='editProduct(${JSON.stringify(product)})'
                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                Edit
            </button>
            <button onclick="deleteProduct(${product.product_id}, '${
                product.nama
            }')"
                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                Hapus
            </button>
            
        </div>
    </div>
    
    <!-- Created and Updated Info - Positioned absolutely at bottom right -->
    <div class="absolute bottom-4 right-4 text-right text-xs text-gray-500">
        <p>Dibuat: ${new Date(product.created_at).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        })}</p>
        <p>Diperbarui: ${new Date(product.updated_at).toLocaleDateString(
            "id-ID",
            {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            }
        )}</p>
    </div>
`;
            grid.appendChild(row);
        });
    } catch (error) {
        console.error("Gagal load produk:", error);
        loadingSkeleton.classList.add("hidden");
    }
}

async function loadProductStats() {
    try {
        let response = await fetch("/api/products");
        let products = await response.json();

        // Hitung total
        let total = products.length;

        // Hitung active (stok > 0)
        let active = products.filter((p) => p.stok > 0).length;

        // Hitung low stock (misal stok <= 5 tapi masih ada)
        let lowStock = products.filter((p) => p.stok > 0 && p.stok <= 5).length;

        // Hitung out of stock (stok == 0)
        let outOfStock = products.filter((p) => p.stok === 0).length;

        // Tampilkan ke DOM
        document.getElementById("totalProducts").textContent = total;
        document.getElementById("activeProducts").textContent = active;
        document.getElementById("lowStockProducts").textContent = lowStock;
        document.getElementById("outOfStockProducts").textContent = outOfStock;
    } catch (error) {
        console.error("Gagal load stats:", error);
    }
}
// buat skeleton bar panjang
function generateSkeleton(count = 5) {
    const loadingSkeleton = document.getElementById("loadingSkeleton");
    loadingSkeleton.innerHTML = "";
    for (let i = 0; i < count; i++) {
        const skel = document.createElement("div");
        skel.className =
            "animate-pulse bg-gray-100 border rounded-lg h-20 w-full mb-4";
        loadingSkeleton.appendChild(skel);
    }
}

// ======== TAMBAH PRODUCT MODAL ========

// Buka MOdal
window.openAddModal = function () {
    document.getElementById("addProductModal").showModal();
};

// TUTUP MODAL
window.closeAddModal = function () {
    document.getElementById("addProductModal").close();
};

// Preview gambar
document
    .getElementById("addProductImage")
    .addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const preview = document.getElementById("addPreviewImg");
                preview.src = event.target.result;
                document
                    .getElementById("addImagePreview")
                    .classList.remove("hidden");
            };
            reader.readAsDataURL(file);
        }
    });

// Handle submit form
document
    .getElementById("addProductForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Tampilkan spinner
        document.getElementById("addSubmitSpinner").classList.remove("hidden");

        try {
            const response = await fetch("/api/products", {
                method: "POST",
                body: formData,
            });

            if (response.ok) {
                alert("Berhasil! Status: " + response.status);
                closeAddModal();
                window.location.reload();
            } else {
                console.error(
                    "Server error:",
                    response.status,
                    response.statusText
                );
                alert("Gagal menambahkan produk!");
            }
        } catch (error) {
            console.error("Fetch error:", error);
            alert("Terjadi kesalahan.");
        }
    });

// untuk delete product
window.deleteProduct = async function (productId, productName) {
    // Pop-up konfirmasi simpel
    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus produk "${productName}"?`
    );
    if (!confirmed) return;

    try {
        const response = await fetch(
            `http://toko-inventori.test:8080/api/products/${productId}`,
            {
                method: "DELETE",
                headers: { Accept: "application/json" },
            }
        );

        if (response.status === 204) {
            alert("Produk berhasil dihapus");
            // Hapus baris dari tabel kalau ada id row
            document.getElementById(`product-row-${productId}`)?.remove();
            window.location.reload();
        } else if (response.status === 404) {
            const data = await response.json();
            alert(data.message);
        } else {
            const data = await response.json();
            alert("Terjadi kesalahan: " + JSON.stringify(data));
        }
    } catch (error) {
        console.error(error);
        alert("Gagal terhubung ke server");
    }
};

// konfirmasi detele product

window.confirmDelete = function (productId, productName) {
    const confirmAction = confirm(
        `Apakah Anda yakin ingin menghapus produk "${productName}"?`
    );
    if (confirmAction) {
        deleteProduct(productId);
    }
};

//UNTUK EDIT PRODUCT
// Buka modal edit dan isi data
window.editProduct = function (product) {
    document.getElementById("editProductId").value = product.product_id;
    document.getElementById("editProductName").value = product.nama;
    document.getElementById("editProductPrice").value = product.harga;
    document.getElementById("editProductStock").value = product.stok;
    document.getElementById("editProductDescription").value =
        product.deskripsi || "";

    if (product.gambar) {
        document.getElementById(
            "editPreviewImg"
        ).src = `data:image/jpeg;base64,${product.gambar}`;
        document.getElementById("editImagePreview").classList.remove("hidden");
    } else {
        document.getElementById("editPreviewImg").src = "";
        document.getElementById("editImagePreview").classList.add("hidden");
    }

    document.getElementById("editProductModal").showModal();
};

// Preview gambar baru
document
    .getElementById("editProductImage")
    .addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const preview = document.getElementById("editPreviewImg");
                preview.src = event.target.result;
                document
                    .getElementById("editImagePreview")
                    .classList.remove("hidden");
            };
            reader.readAsDataURL(file);
        }
    });

// Submit edit product
document
    .getElementById("editProductForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const productId = formData.get("product_id");
        const fileInput = document.getElementById("editProductImage");

        // Jika tidak ada file gambar baru, kirim sebagai JSON
        if (!fileInput.files[0]) {
            const data = {
                nama:
                    formData.get("nama") ||
                    document.getElementById("editProductName").value,
                harga:
                    formData.get("harga") ||
                    document.getElementById("editProductPrice").value,
                stok:
                    formData.get("stok") ||
                    document.getElementById("editProductStock").value,
                deskripsi:
                    formData.get("deskripsi") ||
                    document.getElementById("editProductDescription").value,
            };

            try {
                const response = await fetch(`/api/products/${productId}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        // Tambahkan ini jika pakai CSRF
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    body: JSON.stringify(data),
                });

                if (!response.ok) throw new Error("Gagal update produk");

                const updatedProduct = await response.json();
                alert("Produk berhasil diperbarui!");
                closeEditModal();
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan saat update produk");
            }
        } else {
            // Jika ada file, pakai FormData dengan POST + method spoofing
            formData.append("_method", "PUT");

            try {
                const response = await fetch(`/api/products/${productId}`, {
                    method: "POST",
                    body: formData,
                });

                if (!response.ok) throw new Error("Gagal update produk");

                const updatedProduct = await response.json();
                alert("Produk berhasil diperbarui!");
                closeEditModal();
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan saat update produk");
            }
        }
    });

// Tutup modal edit
window.closeEditModal = function () {
    document.getElementById("editProductModal").close();
};
// load awal
window.onload = () => {
    generateSkeleton(5);
    loadProducts();
    loadProductStats();
};
