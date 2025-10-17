document.addEventListener("DOMContentLoaded", () => {
    const signupBtn = document.getElementById("openSignupModal");
    const signupModal = document.getElementById("signup_modal");
    const loginModal = document.getElementById("login_modal");

    if (signupBtn) {
        signupBtn.addEventListener("click", () => signupModal.showModal());
    }

    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        signupForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const name = document.getElementById("signupName").value;
            const email = document.getElementById("signupEmail").value;
            const password = document.getElementById("signupPassword").value;
            const no_telp = document.getElementById("signupPhone").value;
            const alamat = document.getElementById("signupAddress").value;

            try {
                const response = await fetch("/api/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation: password, // harus ada untuk validasi Laravel
                        no_telp,
                        alamat,
                    }),
                });

                const data = await response.json();
                console.log(data);

                if (response.ok) {
                    alert("Registrasi berhasil! Silakan login.");

                    signupModal.close(); // tutup modal signup
                    loginModal.showModal(); // buka modal login
                } else {
                    alert(
                        data.message || "Registrasi gagal, periksa data Anda."
                    );
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan, coba lagi.");
            }
        });
    }
});
