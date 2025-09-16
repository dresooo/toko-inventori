document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("materialStockBody");
    if (!tbody) {
        console.error("Elemen 'materialStockBody' tidak ditemukan.");
        return;
    }

    try {
        const res = await fetch("/api/stocks"); // Panggil API stock
        if (!res.ok) {
            throw new Error(
                `Gagal memuat data: ${res.status} ${res.statusText}`
            );
        }

        const data = await res.json();
        const products = data.products || [];

        tbody.innerHTML = "";

        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-gray-500 py-4">Tidak ada data stok</td>
                </tr>
            `;
            return;
        }

        products.forEach((product, index) => {
            const row = `
                <tr id="product-row-${product.product_id}" class="${
                index % 2 === 0 ? "bg-base-200" : ""
            }">
                    <td class="px-4 py-2">${index + 1}</td>
                    <td class="px-4 py-2">${product.nama}</td>
                    <td class="px-4 py-2">${product.max_production}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error("Terjadi kesalahan saat memuat data:", error);
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-red-500 py-4">Gagal memuat data. Silakan coba lagi.</td>
            </tr>
        `;
    }
});
