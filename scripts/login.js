document.addEventListener('DOMContentLoaded', function (){
    document.getElementById('submit-login-button').addEventListener('click', validateLogin)
});
function validateLogin(event){
    let name = document.getElementById('username').value;
    let password = document.getElementById('password').value;
    let isValid = true;

    if (name === '') {
        isValid = false;
        document.getElementById('usernameError').innerHTML = 'Vyplňte jméno';
        document.getElementById("username").classList.add("error")
    } else {
        document.getElementById("username").classList.remove("error")
        document.getElementById('usernameError').innerHTML = '';
    }

    if (password === '') {
        isValid = false;
        document.getElementById('passwordError').innerHTML = 'Vyplňte heslo';
        document.getElementById("password").classList.add("error");

    } else {
        document.getElementById('passwordError').innerHTML = '';
        document.getElementById("password").classList.remove("error");

    }

    if(!isValid){
        event.preventDefault();
    }
}