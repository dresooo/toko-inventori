document.addEventListener("DOMContentLoaded", async () => {
    const showLoading = () => (document.body.style.cursor = "wait");
    const hideLoading = () => (document.body.style.cursor = "default");

    try {
        showLoading();

        const res = await fetch("/api/dashboard");
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

        const data = await res.json();

        // ==== Format Rupiah ====
        const formatRupiah = (amount) =>
            "Rp " + new Intl.NumberFormat("id-ID").format(amount);

        // ==== Animasi ====
        const animateNumber = (
            el,
            target,
            duration = 1000,
            isRupiah = false
        ) => {
            let current = 0;
            const step = target / (duration / 16);
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                const val = Math.floor(current);
                el.textContent = isRupiah
                    ? formatRupiah(val)
                    : val.toLocaleString("id-ID");
            }, 16);
        };

        // ==== Isi data utama ====
        const totalRevenue = data.total_revenue || 0;
        const totalOrders = data.total_orders || 0;
        animateNumber(
            document.getElementById("totalRevenue"),
            totalRevenue,
            1500,
            true
        );
        animateNumber(
            document.getElementById("totalOrders"),
            totalOrders,
            1000
        );

        const totalProductsSold =
            data.top_products?.reduce(
                (sum, p) => sum + Number(p.total_sold || 0),
                0
            ) || 0;
        animateNumber(
            document.getElementById("totalProductsSold"),
            totalProductsSold,
            1200
        );

        const avgOrder = totalOrders > 0 ? totalRevenue / totalOrders : 0;
        animateNumber(
            document.getElementById("averageOrder"),
            avgOrder,
            1300,
            true
        );

        // ==== Chart Penjualan ====
        const ctx = document.getElementById("salesChart");
        if (ctx) {
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels:
                        data.sales_by_month?.map(
                            (m) => m.month_name || `Bulan ${m.month}`
                        ) || [],
                    datasets: [
                        {
                            label: "Penjualan (Rp)",
                            data:
                                data.sales_by_month?.map(
                                    (m) => m.total_sales || 0
                                ) || [],
                            backgroundColor: "rgba(147, 51, 234, 0.8)",
                            borderColor: "rgba(147, 51, 234, 1)",
                            borderWidth: 2,
                            borderRadius: 12,
                            hoverBackgroundColor: "rgba(126, 34, 206, 0.9)",
                            barThickness: 40,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });
        }

        // ==== Top Produk ====
        const topList = document.getElementById("topProducts");
        const topProducts = data.top_products || [];
        if (topProducts.length === 0) {
            topList.innerHTML = `<p class="text-gray-500 text-center py-6 text-sm">Belum ada data produk terlaris</p>`;
        } else {
            const colors = [
                {
                    bg: "bg-gradient-to-r from-yellow-400 to-orange-400",
                    emoji: "🥇",
                },
                {
                    bg: "bg-gradient-to-r from-gray-300 to-gray-400",
                    emoji: "🥈",
                },
                {
                    bg: "bg-gradient-to-r from-orange-400 to-orange-600",
                    emoji: "🥉",
                },
            ];

            topProducts.slice(0, 3).forEach((p, i) => {
                const c = colors[i];
                const div = document.createElement("div");
                div.className =
                    "relative flex items-center p-4 rounded-xl bg-gray-100 hover:bg-purple-50 transition-all duration-300 border-l-4 border-purple-500";
                div.innerHTML = `
                    <div class="flex items-center justify-center w-10 h-10 ${
                        c.bg
                    } text-white rounded-full mr-4 shadow-lg">
                        ${c.emoji}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 text-sm mb-1 truncate">${
                            p.product?.nama || "Unknown Product"
                        }</div>
                        <div class="text-xs text-gray-500">
                            <i class="fas fa-box-open mr-1.5"></i>
                            ${p.total_sold || 0} unit terjual
                        </div>
                    </div>`;
                topList.appendChild(div);
            });
        }

        hideLoading();
    } catch (err) {
        console.error("Gagal memuat data dashboard:", err);
        hideLoading();
    }
});

// ==== FIX warna OKLCH agar html2canvas tidak error ====
function replaceOKLCHColorsDeep(element) {
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_ELEMENT,
        null,
        false
    );

    while (walker.nextNode()) {
        const el = walker.currentNode;
        const style = getComputedStyle(el);

        // Ubah semua properti dengan nilai oklch
        for (const prop of [
            "color",
            "background",
            "backgroundColor",
            "borderColor",
        ]) {
            const val = style[prop];
            if (val && val.includes("oklch")) {
                // Ganti dengan warna aman
                el.style[prop] = prop.includes("background")
                    ? "rgb(255,255,255)"
                    : "rgb(0,0,0)";
            }
        }
    }
}

// ==== Export Dashboard Report ke PDF (Text-based) ====
document.getElementById("downloadPDF")?.addEventListener("click", async () => {
    try {
        const res = await fetch("/api/dashboard");
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

        const data = await res.json();
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // === Header ===
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text("LAPORAN DASHBOARD PENJUALAN", 105, 20, { align: "center" });

        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);
        doc.text(
            `Tanggal Cetak: ${new Date().toLocaleDateString("id-ID")}`,
            20,
            30
        );

        doc.setLineWidth(0.5);
        doc.line(20, 33, 190, 33);

        // === Data Utama ===
        let y = 45;
        const lineHeight = 8;
        const formatRupiah = (num) =>
            "Rp " + new Intl.NumberFormat("id-ID").format(num || 0);

        doc.setFont("helvetica", "bold");
        doc.text("Ringkasan Data", 20, y);
        y += lineHeight;

        doc.setFont("helvetica", "normal");
        doc.text(
            `Total Pendapatan : ${formatRupiah(data.total_revenue || 0)}`,
            20,
            y
        );
        y += lineHeight;
        doc.text(`Total Pesanan    : ${data.total_orders || 0}`, 20, y);
        y += lineHeight;

        const totalProductsSold =
            data.top_products?.reduce(
                (sum, p) => sum + Number(p.total_sold || 0),
                0
            ) || 0;
        doc.text(`Total Produk Terjual : ${totalProductsSold}`, 20, y);
        y += lineHeight;

        const avgOrder =
            (data.total_orders && data.total_orders > 0
                ? data.total_revenue / data.total_orders
                : 0) || 0;
        doc.text(`Rata-rata Nilai Pesanan : ${formatRupiah(avgOrder)}`, 20, y);
        y += lineHeight + 5;

        // === Penjualan per Bulan ===
        doc.setFont("helvetica", "bold");
        doc.text("Penjualan per Bulan", 20, y);
        y += lineHeight;

        doc.setFont("helvetica", "normal");
        const sales = data.sales_by_month || [];
        if (sales.length === 0) {
            doc.text("- Belum ada data penjualan bulanan -", 25, y);
            y += lineHeight;
        } else {
            sales.forEach((m) => {
                doc.text(
                    `${m.month_name || "Bulan " + m.month} : ${formatRupiah(
                        m.total_sales
                    )}`,
                    25,
                    y
                );
                y += lineHeight;
            });
        }

        y += 8;
        // === Produk Terlaris ===
        doc.setFont("helvetica", "bold");
        doc.text("Top 3 Produk Terlaris", 20, y);
        y += lineHeight;

        doc.setFont("helvetica", "normal");
        const topProducts = data.top_products || [];
        if (topProducts.length === 0) {
            doc.text("- Belum ada data produk terlaris -", 25, y);
            y += lineHeight;
        } else {
            topProducts.slice(0, 3).forEach((p, i) => {
                const rank = i + 1;
                const name = p.product?.nama || "Produk Tidak Diketahui";
                const sold = p.total_sold || 0;
                doc.text(`${rank}. ${name} - ${sold} unit terjual`, 25, y);
                y += lineHeight;
            });
        }

        // === Footer ===
        y += 10;
        doc.setLineWidth(0.3);
        doc.line(20, y, 190, y);
        y += 8;
        doc.setFontSize(10);
        doc.text(
            "Laporan ini dihasilkan otomatis dari sistem dashboard.",
            105,
            y,
            { align: "center" }
        );

        // Simpan PDF
        doc.save(
            `Laporan-Dashboard-${new Date().toISOString().slice(0, 10)}.pdf`
        );
    } catch (err) {
        console.error("Gagal membuat laporan PDF:", err);
        alert("Gagal membuat laporan PDF. Coba lagi nanti.");
    }
});
