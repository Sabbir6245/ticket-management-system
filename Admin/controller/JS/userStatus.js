function updateStatus(id, status) {
    let formData = new FormData();
    formData.append("id", id);
    formData.append("status", status);

    fetch("../controller/updateUserStatus.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            document.getElementById("status-" + id).innerText = status;
           
        } else {
            alert("Update failed");
        }
    });
}
