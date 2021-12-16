//Validiate fields
document.querySelector("#add-friend").onsubmit = function(event) {
    let friend_name = document.querySelector("#form-friend").value.trim();
    if(friend_name.length <= 0) {
        document.querySelector("#form-friend").classList.add("is-invalid");
        document.querySelector("#error").textContent = "";
        event.preventDefault();
    }
};