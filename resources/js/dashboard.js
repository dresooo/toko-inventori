document.addEventListener("DOMContentLoaded", () => {
    const role = localStorage.getItem("user_role"); // simpan role pas login
    if (role !== "admin") {
        window.location.href = "/homepage";
    }
});
