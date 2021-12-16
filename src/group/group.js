//Validiate fields
document.querySelector("#add-group").onsubmit = function(event) {
    let friend_name = document.querySelector("#form-group").value.trim();
    if(friend_name.length <= 0) {
        document.querySelector("#form-group").classList.add("is-invalid");
        document.querySelector("#error").textContent = "";
        event.preventDefault();
    }
};