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
                    <td>
                        <button onclick='editProduct(${productId})' class="btn btn-xs btn-warning">Edit</button>
                        <button onclick='deleteProduct(${productId})' class="btn btn-xs btn-error">Hapus</button>
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
