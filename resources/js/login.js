document.addEventListener("DOMContentLoaded", () => {
    updateNavbar();
    // tampilkan modal
    const loginBtn = document.getElementById("openLoginModal");
    const loginModal = document.getElementById("login_modal");

    loginBtn.addEventListener("click", () => loginModal.showModal());

    // handle login form submit
    document
        .getElementById("loginForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();
            const email = document.getElementById("loginEmail").value;
            const password = document.getElementById("loginPassword").value;

            try {
                const response = await fetch(
                    "http://127.0.0.1:8000/api/login",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ email, password }),
                    }
                );
                const data = await response.json();
                // console.log(data); // ini bisas
                if (response.ok) {
                    console.log(data); // ini bisa
                    localStorage.setItem("access_token", data.access_token);
                    localStorage.setItem("user_name", data.user.name);
                    localStorage.setItem("user_type", data.user.user_type);
                    localStorage.setItem("user_id", data.user.id);
                    //update navbar ketika berhasil login
                    updateNavbar();
                    loginModal.close(); // tutup modal setelah login sukses

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

    if (userName) {
        // Tampilkan nama user
        navbarUser.innerText = `${userName} `;
        navbarUser.style.display = "inline-block";

        // Sembunyikan tombol login
        if (btnLogin) btnLogin.style.display = "none";
    } else {
        // Jika belum login, tampilkan tombol login
        navbarUser.style.display = "none";
        if (btnLogin) btnLogin.style.display = "inline-block";
    }
}
