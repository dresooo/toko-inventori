document.addEventListener("DOMContentLoaded", () => {
    // Set ID dari Frontend
    const navbarUser = document.getElementById("navbarUser");
    const profileModal = document.getElementById("profileModal");
    const editProfileModal = document.getElementById("editProfileModal");
    const editProfileBtn = document.getElementById("editProfileBtn");
    const editProfileForm = document.getElementById("editProfileForm");

    const apiBase = "/api"; // sesuaikan dgn base API kamu
    const token = localStorage.getItem("access_token"); // ambil token dari localStorage setelah login

    let currentUser = null;

    // 🔹 Ambil data user yang login
    async function loadUserProfile() {
        if (!token) {
            console.warn("Token tidak ditemukan, user belum login.");
            return;
        }
        try {
            let res = await fetch(`${apiBase}/user`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
            });
            if (!res.ok) throw new Error("Gagal ambil profil");
            currentUser = await res.json();

            // Tampilkan nama di navbar
            navbarUser.textContent = currentUser.name;

            // Isi data ke modal profil
            document.getElementById("profileName").textContent =
                currentUser.name;
            document.getElementById("profileEmail").textContent =
                currentUser.email;
            document.getElementById("profileTelp").textContent =
                currentUser.no_telp ?? "-";
            document.getElementById("profileAlamat").textContent =
                currentUser.alamat ?? "-";
        } catch (err) {
            console.error(err);
        }
    }

    // Klik nama user -> buka modal profil
    navbarUser.addEventListener("click", () => {
        profileModal.showModal();
    });

    // Klik "Edit Profil" -> buka modal edit
    editProfileBtn.addEventListener("click", () => {
        // isi form dengan data lama
        document.getElementById("editName").value = currentUser.name;
        document.getElementById("editEmail").value = currentUser.email;
        document.getElementById("editTelp").value = currentUser.no_telp ?? "";
        document.getElementById("editAlamat").value = currentUser.alamat ?? "";

        profileModal.close();
        editProfileModal.showModal();
    });

    // Submit edit profil
    editProfileForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const newData = {
            name: document.getElementById("editName").value,
            email: document.getElementById("editEmail").value,
            no_telp: document.getElementById("editTelp").value,
            alamat: document.getElementById("editAlamat").value,
        };
        //Update Data ke Database
        try {
            let res = await fetch(`${apiBase}/user/${currentUser.id}`, {
                method: "PUT",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(newData),
            });

            if (!res.ok) throw new Error("Gagal update profil");

            let updatedUser = await res.json();
            currentUser = updatedUser;

            // update UI
            navbarUser.textContent = updatedUser.name;
            profileModal.close();
            editProfileModal.close();

            alert("Profil berhasil diperbarui!");
            loadUserProfile(); // refresh data
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan saat update profil");
        }
    });

    // Jalankan pertama kali
    loadUserProfile();
});
