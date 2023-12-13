document.addEventListener('DOMContentLoaded', function (){
    // Your existing JavaScript code here
document.getElementById('submit-registration-button').addEventListener('click', validateRegistration)


});
function validateRegistration(event){
    let name = document.getElementById('username').value;
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirm-password').value;
    let agreed = document.getElementById('agreement-checkbox');
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

    if (confirmPassword !== password){
        isValid = false;
        document.getElementById('confirm-passwordError').innerHTML = 'Hesla se neshodují';
        document.getElementById("confirm-password").classList.add("error");
    } else {
        document.getElementById('confirm-passwordError').innerHTML = '';
        document.getElementById("confirm-password").classList.remove("error");
    }

    if (!agreed.checked){
         isValid = false;
        document.getElementById('genericError').innerHTML = 'Pro registraci je nutné souhlasit s podmínkami';
    }
    else{
        document.getElementById('genericError').innerHTML = '';
    }

    if(!isValid){
        event.preventDefault();
    }
}