document.addEventListener("DOMContentLoaded", () => {

    //muat semua modal form dan tombol setiap kali halaman di load
    updateNavbar();
    // tampilkan modal
    const loginBtn = document.getElementById("openLoginModal");
    const loginModal = document.getElementById("login_modal");

    //Tampilkan Login Modal Ketika Click Login
    loginBtn.addEventListener("click", () => loginModal.showModal());

    // handle login form submit
    document
        .getElementById("loginForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();
            //ambil value dari field email dan password
            const email = document.getElementById("loginEmail").value;
            const password = document.getElementById("loginPassword").value;

            try {
                //kirim post request ke api
                const response = await fetch("/api/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ email, password }),
                });

                //respond dari api yang di hit
                const data = await response.json();
                // console.log(data); // 
                if (response.ok) {
                    console.log(data); 
                    localStorage.setItem("access_token", data.access_token);
                    localStorage.setItem("user_name", data.user.name);
                    localStorage.setItem("user_type", data.user.user_type);
                    localStorage.setItem("user_id", data.user.id);
                    //update navbar ketika berhasil login
                    updateNavbar();
                    loginModal.close(); // tutup modal setelah login sukses
                    
                    //redirect user berfasarkan user type
                    if (data.user.user_type === "admin") {
                        window.location.href = "/dashboard";
                    } else {
                        window.location.href = "/homepage";
                    }
                } else {
                    alert(data.message || "Login gagal");
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan, coba lagi.");
            }
        });
});

function updateNavbar() {
    const userName = localStorage.getItem("user_name");
    const btnLogin = document.getElementById("openLoginModal");
    const navbarUser = document.getElementById("navbarUser");
    const notifBtn = document.getElementById("notifBtn"); // tombol notifikasi

    if (userName) {
        // Tampilkan nama user
        if (navbarUser) {
            navbarUser.innerText = `${userName} `;
            navbarUser.style.display = "inline-block";
        }

        // Sembunyikan tombol login
        if (btnLogin) btnLogin.style.display = "none";

        // Tampilkan tombol notifikasi
        if (notifBtn) notifBtn.style.display = "inline-block";
    } else {
        // Jika belum login, tampilkan tombol login
        if (navbarUser) navbarUser.style.display = "none";
        if (btnLogin) btnLogin.style.display = "inline-block";

        // Sembunyikan tombol notifikasi
        if (notifBtn) notifBtn.style.display = "none";
    }
}
