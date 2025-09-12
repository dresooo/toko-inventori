async function loadProducts() {
    let response = await fetch("/api/products");
    let products = await response.json();

    const grid = document.getElementById("product-grid");
    grid.innerHTML = "";

    products.forEach((product, index) => {
        // Card
        const card = document.createElement("div");
        card.className =
            "card bg-base-100 w-80 lg:w-auto shadow-lg hover:shadow-xl transition-shadow duration-300";

        card.innerHTML = `
            <figure class="aspect-square overflow-hidden">
                <img src="data:image/jpeg;base64,${product.gambar}"
                    alt="${product.nama}"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                    onclick="openProductModal(${index})"/>
            </figure>
            <div class="card-body">
                <h2 class="card-title text-lg font-bold">${product.nama}</h2>
                <p class="text-sm text-gray-600 mb-2">${product.deskripsi.substring(
                    0,
                    100
                )}...</p>
                <div class="text-xl font-bold text-primary mb-4">
                    Rp ${Number(product.harga).toLocaleString("id-ID")}
                </div>ok
                <div class="card-actions justify-end">
                    <button class="btn btn-primary btn-sm" onclick="alert('Buy Now clicked!')">Buy Now</button>
                    <button class="btn btn-outline btn-sm" onclick="openProductModal(${index})">Detail</button>
                </div>
            </div>
        `;
        grid.appendChild(card);

        // Modal
        const modal = document.createElement("dialog");
        modal.id = `product_modal_${index}`;
        modal.className = "modal";
        modal.innerHTML = `
            <div class="modal-box max-w-4xl p-6 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="relative group">
                            <img src="data:image/jpeg;base64,${product.gambar}"
                                alt="${product.nama}"
                                class="w-full aspect-square object-cover rounded-xl shadow-lg group-hover:shadow-xl transition-shadow duration-300" />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 rounded-xl flex items-center justify-center opacity-0"></div>
                        </div>
                    </div>
                    <div class="flex flex-col h-full space-y-6">
                        <div class="bg-primary bg-opacity-10 p-4 rounded-xl border-l-4 border-primary">
                            <p class="text-sm text-white mb-1">Harga</p>
                            <p class="text-3xl font-bold text-white">
                                Rp ${Number(product.harga).toLocaleString(
                                    "id-ID"
                                )}
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-primary rounded-full"></div>
                                <span class="text-gray-600">Stok:</span>
                                <span class="font-medium ${
                                    product.stok > 0
                                        ? "text-green-600"
                                        : "text-red-600"
                                }">
                                    ${
                                        product.stok > 0
                                            ? product.stok + " tersedia"
                                            : "Habis"
                                    }
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-lg flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Deskripsi Produk
                            </h4>
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <p class="text-gray-700 leading-relaxed">${
                                    product.deskripsi
                                }</p>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-auto">
                            <button class="btn btn-primary flex-1 gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5-2.5M7 13l2.5 2.5M17 17a2 2 0 11-4 0 2 2 0 014 0zM9 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>

            
        `;
        document.body.appendChild(modal);
    });
}

// buka modal
window.openProductModal = function (index) {
    const modal = document.getElementById(`product_modal_${index}`);
    if (modal) modal.showModal();
};

window.onload = loadProducts;
