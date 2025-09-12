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
                <tr class="${index % 2 === 0 ? "bg-base-200" : ""}">
                    <th>${index + 1}</th>
                    <td>${item.nama}</td>
                    <td>${item.stock_quantity}</td>
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
                        <button class="btn btn-xs btn-warning">Edit</button>
                        <button class="btn btn-xs btn-error">Hapus</button>
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
