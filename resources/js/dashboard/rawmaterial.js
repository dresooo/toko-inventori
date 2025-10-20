document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("materialBody");
    if (!tbody) {
        console.error("Elemen 'materialBody' tidak ditemukan.");
        return;
    }

    try {
        const res = await fetch("/api/raw-materials");
        if (!res.ok) {
            throw new Error(
                `Gagal memuat data: ${res.status} ${res.statusText}`
            );
        }

        const data = await res.json();

        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            `;
            return;
        }

        data.forEach((item, index) => {
            const row = `
                <tr id="rawmaterial-row-${item.raw_material_id}" class="${
                index % 2 === 0 ? "bg-base-200" : ""
            }">
                    <th>${index + 1}</th>
                    <td>${item.nama}</td>
                    <td>${item.stock_quantity}</td>
                    <td>${item.minimum_stock}</td>
                    <td>${item.satuan}</td>
                    
                    <td>Rp ${parseFloat(item.harga_beli).toLocaleString(
                        "id-ID"
                    )}</td>
                    <td>${new Date(item.created_at).toLocaleDateString(
                        "id-ID"
                    )}</td>
                    <td>${new Date(item.updated_at).toLocaleDateString(
                        "id-ID"
                    )}</td>
                    <td>
                        <button onclick='editRawMaterial(${JSON.stringify(
                            item
                        )})' class="btn btn-xs btn-warning">Edit</button>
                        <button onclick="confirmDeleteRawMaterial(${
                            item.raw_material_id
                        }, '${
                item.nama
            }')" class="btn btn-xs btn-error">Hapus</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error("Terjadi kesalahan saat memuat data:", error);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-red-500">Gagal memuat data. Silakan coba lagi.</td>
            </tr>
        `;
    }
});

// modul untuk tambah raw material
window.openAddModal = function () {
    document.getElementById("addRawMaterialModal").showModal();
};

// TUTUP MODAL
window.closeAddModal = function () {
    document.getElementById("addRawMaterialModal").close();
};

document
    .getElementById("addRawMaterialForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // tampilkan spinner
        document
            .getElementById("addRawMaterialSpinner")
            .classList.remove("hidden");

        try {
            const response = await fetch("/api/raw-materials", {
                method: "POST",
                body: formData,
            });

            if (response.ok) {
                alert(
                    "Raw material berhasil ditambahkan! Status: " +
                        response.status
                );
                closeAddModal();
                window.location.reload();
            } else {
                const err = await response.json();
                console.error("Server error:", response.status, err);
                alert("Gagal menambahkan raw material!");
            }
        } catch (error) {
            console.error("Fetch error:", error);
            alert("Terjadi kesalahan pada request.");
        } finally {
            // sembunyikan spinner
            document
                .getElementById("addRawMaterialSpinner")
                .classList.add("hidden");
        }
    });

// Hapus raw material
window.deleteRawMaterial = async function (materialId, materialName) {
    // Pop-up konfirmasi simpel
    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus bahan baku "${materialName}"?`
    );
    if (!confirmed) return;

    try {
        const response = await fetch(`/api/raw-materials/${materialId}`, {
            method: "DELETE",
            headers: { Accept: "application/json" },
        });

        if (response.status === 204) {
            alert("Bahan baku berhasil dihapus");
            // Hapus baris dari tabel kalau ada id row
            document.getElementById(`rawmaterial-row-${materialId}`)?.remove();
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

// Konfirmasi delete raw material
window.confirmDeleteRawMaterial = function (materialId, materialName) {
    deleteRawMaterial(materialId, materialName);
};

//EDIT MODEL RAW PRODUCT
// Buka modal edit raw material dan isi data
window.editRawMaterial = function (material) {
    document.getElementById("editRawMaterialId").value =
        material.raw_material_id;
    document.getElementById("editRawMaterialName").value = material.nama;
    document.getElementById("editRawMaterialStock").value =
        material.stock_quantity;
    document.getElementById("editRawMaterialUnit").value = material.satuan;
    document.getElementById("editRawMaterialPrice").value =
        material.harga_beli ?? "";
    document.getElementById("editRawMaterialMinStock").value =
        material.minimum_stock ?? "";

    document.getElementById("editRawMaterialModal").showModal();
};

// Submit edit raw material
document
    .getElementById("editRawMaterialForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const materialId = formData.get("raw_material_id");

        // Spinner ON
        document
            .getElementById("editRawSubmitSpinner")
            .classList.remove("hidden");

        try {
            const data = {
                nama: formData.get("nama"),
                stock_quantity: formData.get("stock_quantity"),
                satuan: formData.get("satuan"),
                harga_beli: formData.get("harga_beli"),
                minimum_stock: formData.get("minimum_stock"),
            };

            const response = await fetch(`/api/raw-materials/${materialId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) throw new Error("Gagal update raw material");

            await response.json();
            alert("Bahan baku berhasil diperbarui!");
            closeEditRawMaterial();
            window.location.reload();
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan saat update bahan baku");
        } finally {
            // Spinner OFF
            document
                .getElementById("editRawSubmitSpinner")
                .classList.add("hidden");
        }
    });
// Tutup modal edit raw material
window.closeEditRawMaterial = function () {
    document.getElementById("editRawMaterialModal").close();
};
