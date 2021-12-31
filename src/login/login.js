var clicked = "";

//Validiate fields
document.querySelector("#login-form").onsubmit = function(event) {
    document.querySelector("#form-username").value = document.querySelector("#form-username").value.trim();
    let username = document.querySelector("#form-username").value.trim();
    let password = document.querySelector("#form-password").value.trim();

    let valid = true;

    if(username.length <= 0 || username.length > 45 || password.length <= 0) {
        if(username.length <= 0 || username.length > 45) {
            document.querySelector("#form-username").classList.add("is-invalid");
        }
        if(password.length <= 0) {
            document.querySelector("#form-password").classList.add("is-invalid");
        }
        event.preventDefault();
        valid = false;
    }
    //Check if it is the register and popup warning
    if(valid && clicked == "register") {
        return confirm('This is a student project, please do not use any sort of sensitive password. Are you sure you want to continue making the account?');
    }
};

document.querySelector("#register-btn").onclick = function(event) {
    clicked = "register";
    document.querySelector("#login-text").textContent = "Register";
    document.querySelector(".js-remove").remove();
}

document.querySelector("#login-btn").onclick = function(event) {
    clicked = "login";
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
