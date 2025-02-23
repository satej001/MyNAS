function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    if (sidebar.style.right === "0px") {
        sidebar.style.right = "-250px";
        overlay.style.display = "none";
    } else {
        sidebar.style.right = "0px";
        overlay.style.display = "block";
    }
}

// Close sidebar when clicking outside
document.addEventListener("click", function (event) {
    const sidebar = document.getElementById("sidebar");
    const profileIcon = document.querySelector(".profile-icon");
    const overlay = document.getElementById("overlay");

    // Check if the click is outside the sidebar & profile icon
    if (!sidebar.contains(event.target) && !profileIcon.contains(event.target)) {
        sidebar.style.right = "-250px";
        overlay.style.display = "none";
    }
});
