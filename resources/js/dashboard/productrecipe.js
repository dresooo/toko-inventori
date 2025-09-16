document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("recipeBody");
    if (!tbody) return;

    try {
        const res = await fetch("/api/product-recipe");
        if (!res.ok)
            throw new Error(
                `Gagal memuat data: ${res.status} ${res.statusText}`
            );
        const data = await res.json();

        // Kelompokkan resep berdasarkan product_id
        const grouped = data.reduce((acc, item) => {
            if (!acc[item.product_id]) acc[item.product_id] = [];
            acc[item.product_id].push(item);
            return acc;
        }, {});

        tbody.innerHTML = "";

        Object.entries(grouped).forEach(([productId, recipes], index) => {
            console.log(`Recipe ${index}:`, recipes);
            // Ambil nama produk dari recipe pertama dalam grup
            const productName =
                recipes[0]?.product?.nama || "Produk tidak ditemukan";
            // Gabungkan catatan bahan menjadi satu string
            const bahanList = recipes
                .map(
                    (r) =>
                        `${
                            r.raw_material?.nama || "Material tidak ditemukan"
                        } - Qty: ${r.quantity_needed} / Product`
                )
                .join("<br>");

            const row = `
                <tr class="${index % 2 === 0 ? "bg-base-200" : ""}">
                    <th>${index + 1}</th>
                    <td>${productName}</td>
                    <td>${bahanList}</td>
                    <td>${new Date(recipes[0].created_at).toLocaleDateString(
                        "id-ID"
                    )}</td>
                    <td>${new Date(
                        recipes[recipes.length - 1].updated_at
                    ).toLocaleDateString("id-ID")}</td>
                    <td class="flex space-x-2">
                        <button type="button" 
                                class="flex items-center justify-center px-2 py-1 rounded-md bg-yellow-500 text-white 
                                       hover:bg-yellow-600 shadow focus:outline-none focus:ring-1 focus:ring-yellow-400 text-xs edit-recipe-btn"
                                data-id="${productId}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                 stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-.878.515l-3.536.884a.5.5 0 01-.61-.61l.884-3.536a2 2 0 01.515-.878z" />
                            </svg>
                            <span class="ml-1">Edit</span>
                        </button>

                        <button type="button"
                                class="btn btn-xs btn-error delete-product-btn"
                                data-id="${productId}">
                            Hapus
                        </button>
  </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500">Gagal memuat data</td></tr>`;
    }
});

// OPEN MODAL
window.openRecipeModal = function () {
    document.getElementById("addRecipeModal").showModal();
};

// CLOSE MODAL
window.closeRecipeModal = function () {
    document.getElementById("addRecipeModal").close();
};

// Simpan semua produk hasil fetch di sini
let productsCache = [];
let rawMaterialsCache = [];

// TAMBAH BARIS INPUT
document
    .getElementById("addRecipeRowBtn")
    .addEventListener("click", function () {
        const container = document.getElementById("recipe-container");

        const newRow = document.createElement("div");
        newRow.className = "recipe-item grid grid-cols-3 gap-4";

        // Buat opsi produk
        let productOptions = '<option value="">-- Pilih Produk --</option>';
        productsCache.forEach((p) => {
            productOptions += `<option value="${p.product_id}">${p.nama}</option>`;
        });

        // Buat opsi raw material
        let rawOptions = '<option value="">-- Pilih Raw Material --</option>';
        rawMaterialsCache.forEach((r) => {
            rawOptions += `<option value="${r.raw_material_id}">${r.nama}</option>`;
        });

        newRow.innerHTML = `
        <div>
            <label class="block mb-1 font-medium">Product *</label>
            <select name="product_id[]" required
                class="select select-bordered mt-2 w-full">
                ${productOptions}
            </select>
        </div>
        <div>
            <label class="block mb-1 font-medium">Raw Material *</label>
            <select name="raw_material_id[]" required
                class="select select-bordered mt-2 w-full">
                ${rawOptions}
            </select>
        </div>
        <div>
            <div class="flex justify-between items-center">
                <label class="block mb-1 font-medium">Quantity Needed *</label>
                <button type="button" class="btn btn-error btn-xs remove-row">X</button>
            </div>
            <input type="number" name="quantity_needed[]" placeholder="0" required min="0"
              class="input input-bordered mt-2 w-full" />
        </div>
        `;

        container.appendChild(newRow);
    });

/// FETCH Produk & Raw Materials
document.addEventListener("DOMContentLoaded", function () {
    // Fetch produk
    fetch("/api/products")
        .then((res) => res.json())
        .then((products) => {
            productsCache = products;
            let select = document.getElementById("productSelect");
            products.forEach((p) => {
                let opt = document.createElement("option");
                opt.value = p.product_id;
                opt.textContent = p.nama;
                select.appendChild(opt);
            });
        })
        .catch((err) => console.error(err));

    // Fetch raw materials
    fetch("/api/raw-materials")
        .then((res) => res.json())
        .then((raws) => {
            rawMaterialsCache = raws;
            let select = document.getElementById("rawMaterialSelect");
            raws.forEach((r) => {
                let opt = document.createElement("option");
                opt.value = r.raw_material_id;
                opt.textContent = r.nama;
                select.appendChild(opt);
            });
        })
        .catch((err) => console.error(err));
});

// Hapus row kalau tombol X ditekan
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-row")) {
        e.target.closest(".recipe-item").remove();
    }
});

//simpan data ke db

document
    .getElementById("addRecipeForm")
    .addEventListener("submit", function (e) {
        e.preventDefault(); // biar ga reload page

        // Ambil semua data form
        const formData = new FormData(this);

        // Karena name pakai array (product_id[], raw_material_id[], quantity_needed[])
        // kita harus looping biar rapi
        const productIds = formData.getAll("product_id[]");
        const rawIds = formData.getAll("raw_material_id[]");
        const quantities = formData.getAll("quantity_needed[]");

        let payloads = [];
        for (let i = 0; i < productIds.length; i++) {
            payloads.push({
                product_id: productIds[i],
                raw_material_id: rawIds[i],
                quantity_needed: quantities[i],
            });
        }

        // Kirim satu per satu ke API (atau bisa bulk tergantung backend)
        Promise.all(
            payloads.map((item) =>
                fetch("/api/product-recipe", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(item),
                }).then(async (res) => {
                    if (!res.ok) {
                        const errorData = await res.json();
                        throw errorData; // lempar ke catch
                    }
                    return res.json();
                })
            )
        )
            .then((results) => {
                console.log("Berhasil simpan:", results);
                alert("Semua recipe berhasil disimpan!");
                window.location.reload();
                // document.getElementById("addRecipeForm").reset();
            })
            .catch((err) => {
                console.error("Error simpan:", err);
                alert(
                    "Gagal menyimpan recipe: " +
                        (err.message || "Periksa input!")
                );
                window.location.reload();
            });
    });

// Pastikan tidak ada duplikasi event listener
if (!window.recipeEditInitialized) {
    // EVENT LISTENER UNTUK TOMBOL EDIT
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("edit-recipe-btn")) {
            const productId = e.target.getAttribute("data-id");
            console.log("Product ID dari tombol:", productId);
            openEditModal(productId);
        }
    });

    window.recipeEditInitialized = true;
}

// EVENT LISTENER UNTUK TOMBOL EDIT
document.addEventListener("click", function (e) {
    if (
        e.target.classList.contains("edit-recipe-btn") ||
        e.target.closest(".edit-recipe-btn")
    ) {
        const button = e.target.classList.contains("edit-recipe-btn")
            ? e.target
            : e.target.closest(".edit-recipe-btn");
        const productId = button.getAttribute("data-id");
        console.log("Product ID dari tombol:", productId);
        openEditModal(productId);
    }
});

// OPEN EDIT MODAL
window.openEditModal = function (productId) {
    const modal = document.getElementById("editRecipeModal");
    const container = document.getElementById("edit-recipe-container");

    if (!modal || !container)
        return alert("Modal atau container tidak ditemukan!");
    if (!productId) return alert("Product ID tidak valid!");

    container.innerHTML = '<div class="text-center py-4">Loading...</div>';
    modal.showModal();

    fetch(`/api/product-recipe/${productId}`)
        .then((res) => {
            if (!res.ok) throw new Error("HTTP error! status: " + res.status);
            return res.json();
        })
        .then((data) => {
            if (!Array.isArray(data)) {
                if (data && data.data && Array.isArray(data.data)) {
                    data = data.data;
                } else throw new Error("Format data tidak valid");
            }

            container.innerHTML = "";

            if (data.length === 0) {
                container.innerHTML =
                    '<p class="text-center text-gray-500">Tidak ada recipe untuk produk ini.</p>';
                return;
            }

            data.forEach((recipe, index) => {
                const newRow = document.createElement("div");
                newRow.className = "recipe-item grid grid-cols-3 gap-4";

                // Options produk
                let productOptions =
                    '<option value="">-- Pilih Produk --</option>';
                if (Array.isArray(productsCache)) {
                    productsCache.forEach((p) => {
                        const selected =
                            p.product_id == recipe.product_id ? "selected" : "";
                        productOptions += `<option value="${p.product_id}" ${selected}>${p.nama}</option>`;
                    });
                }

                // Options raw material
                let rawOptions =
                    '<option value="">-- Pilih Raw Material --</option>';
                if (Array.isArray(rawMaterialsCache)) {
                    rawMaterialsCache.forEach((r) => {
                        const selected =
                            r.raw_material_id == recipe.raw_material_id
                                ? "selected"
                                : "";
                        rawOptions += `<option value="${r.raw_material_id}" ${selected}>${r.nama}</option>`;
                    });
                }

                newRow.innerHTML = `
                    <div>
                        <label class="block mb-1 font-medium">Product *</label>
                        <select name="product_id[]" required class="select select-bordered mt-2 w-full">${productOptions}</select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Raw Material *</label>
                        <select name="raw_material_id[]" required class="select select-bordered mt-2 w-full">${rawOptions}</select>
                    </div>
                    <div>
            <div class="flex justify-between items-center">
                <label class="block mb-1 font-medium">Quantity Needed *</label>
                <button type="button" class="btn btn-error btn-xs remove-row">X</button>
            </div>
            <input type="number" name="quantity_needed[]" value="${
                recipe.quantity_needed || 0
            }" required min="0" step="0.01" class="input input-bordered mt-2 w-full" />
            <!-- Perbaiki di sini -->
            <input type="hidden" name="recipe_id[]" value="${
                recipe.recipe_id || ""
            }" />
        </div>
                `;

                container.appendChild(newRow);
            });
        })
        .catch((err) => {
            console.error(err);
            container.innerHTML = `<p class="text-center text-red-500">Error: ${err.message}</p>`;
            alert("Gagal mengambil data recipe: " + err.message);
        });
};

// CLOSE EDIT MODAL
window.closeEditModal = function () {
    const modal = document.getElementById("editRecipeModal");
    if (modal) modal.close();
};

// SIMPAN PERUBAHAN (EDIT ONLY)
document.addEventListener("DOMContentLoaded", function () {
    const editForm = document.getElementById("editRecipeForm");
    if (!editForm) return;

    editForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const recipeIds = formData.getAll("recipe_id[]");
        const productIds = formData.getAll("product_id[]");
        const rawIds = formData.getAll("raw_material_id[]");
        const quantities = formData.getAll("quantity_needed[]");

        if (productIds.length === 0)
            return alert("Tidak ada data recipe untuk disimpan!");

        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = "Menyimpan...";
        }

        let updatePromises = [];

        for (let i = 0; i < productIds.length; i++) {
            if (!recipeIds[i]) {
                alert(
                    "Tidak bisa menyimpan row tanpa Recipe ID! Baris: " +
                        (i + 1)
                );
                return;
            }

            if (!productIds[i] || !rawIds[i] || !quantities[i]) {
                alert("Data pada baris " + (i + 1) + " tidak lengkap!");
                return;
            }

            const payload = {
                product_id: productIds[i],
                raw_material_id: rawIds[i],
                quantity_needed: parseFloat(quantities[i]),
            };

            // Hanya update
            updatePromises.push(
                fetch("/api/product-recipe/" + recipeIds[i], {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                }).then((res) => {
                    if (!res.ok)
                        return res.json().then((err) => {
                            throw new Error(err.message || "Update gagal");
                        });
                    return res.json();
                })
            );
        }

        Promise.all(updatePromises)
            .then((results) => {
                console.log("Berhasil update:", results);
                alert("Semua recipe berhasil diperbarui!");
                closeEditModal();
                window.location.reload();
            })
            .catch((err) => {
                console.error("Error update:", err);
                alert("Gagal update recipe: " + err.message);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Simpan Perubahan";
                }
            });
    });
});
// Hapus row di modal edit (DELETE dari DB jika recipe_id ada)
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-row")) {
        const row = e.target.closest(".recipe-item");
        if (!row) return;

        const recipeIdInput = row.querySelector('input[name="recipe_id[]"]');
        if (!recipeIdInput || !recipeIdInput.value) {
            // Row baru / belum tersimpan di DB -> hapus langsung dari DOM
            row.remove();
            return;
        }

        // Row sudah ada di DB -> hapus via API
        const recipeId = recipeIdInput.value;

        if (!confirm("Apakah yakin ingin menghapus recipe ini?")) return;

        fetch(`/api/product-recipe/${recipeId}`, {
            method: "DELETE",
            headers: { Accept: "application/json" },
        })
            .then((res) => {
                if (!res.ok)
                    return res.json().then((err) => {
                        throw new Error(err.message || "Hapus gagal");
                    });
                return res.json();
            })
            .then((data) => {
                alert("Recipe berhasil dihapus!");
                row.remove();
                window.location.reload();
            })
            .catch((err) => {
                console.error("Error hapus:", err);
                alert("Gagal menghapus recipe: " + err.message);
            });
    }
});

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("delete-product-btn")) {
        const productId = e.target.getAttribute("data-id");
        if (!productId) return alert("Product ID tidak valid!");

        if (
            !confirm(
                "Apakah Anda yakin ingin menghapus semua recipe untuk produk ini?"
            )
        )
            return;

        fetch(`/api/product-recipe/product/${productId}`, {
            method: "DELETE",
            headers: { Accept: "application/json" },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.message) alert(data.message);
                window.location.reload(); // reload halaman agar data update
            })
            .catch((err) => {
                console.error("Gagal menghapus recipe:", err);
                alert("Gagal menghapus recipe: " + err.message);
            });
    }
});
