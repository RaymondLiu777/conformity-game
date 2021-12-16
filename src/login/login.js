//Validiate fields
document.querySelector("#login-form").onsubmit = function(event) {
    document.querySelector("#form-username").value = document.querySelector("#form-username").value.trim();
    let username = document.querySelector("#form-username").value.trim();
    let password = document.querySelector("#form-password").value.trim();

    if(username.length <= 0 || username.length > 45 || password.length <= 0) {
        if(username.length <= 0 || username.length > 45) {
            document.querySelector("#form-username").classList.add("is-invalid");
        }
        if(password.length <= 0) {
            document.querySelector("#form-password").classList.add("is-invalid");
        }
        event.preventDefault();
    }
};

document.querySelector("#register-btn").onclick = function(event) {
    document.querySelector("#login-text").textContent = "Register";
    document.querySelector(".js-remove").remove();
}

let addPasswordLength = false;

document.querySelector("#form-username").oninput = function() {
    //Remove error message
    if(this.classList.contains("is-invalid") && this.value.trim().length > 0) {
        this.classList.remove("is-invalid");
    }
    //Add error message for usernames that are too long
    if(this.value.trim().length > 45) {
        this.classList.add("is-invalid");
        if(!addPasswordLength) {
            addPasswordLength = true;
            document.querySelector("#username-error").textContent = "Username must be less than 45 characters";
        }
    }
    else if (addPasswordLength) {
        addPasswordLength = false;
        document.querySelector("#username-error").textContent = "Please Enter Username";
    }
}

document.querySelector("#form-password").oninput = function() {
    //Remove error message
    if(this.classList.contains("is-invalid") && this.value.trim().length > 0) {
        this.classList.remove("is-invalid");
    }
}
