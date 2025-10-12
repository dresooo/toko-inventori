document.addEventListener("DOMContentLoaded", () => {
    window.logout = async function () {
        const token = localStorage.getItem("access_token");

        if (!token) {
            alert("Belum login");
            return;
        }

        try {
            const response = await fetch("http://127.0.0.1:8000/api/logout", {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            });

            const data = await response.json();
            console.log(data);

            if (response.ok) {
                localStorage.removeItem("access_token");
                localStorage.removeItem("user_name");
                localStorage.removeItem("user_id");
                localStorage.removeItem("user_type");
                alert("Logout berhasil");
                window.location.href = "/homepage";
            } else {
                alert(data.message || "Logout gagal");
            }
        } catch (error) {
            console.error("Error logout:", error);
            alert("Terjadi kesalahan, coba lagi");
        }
    };
});
