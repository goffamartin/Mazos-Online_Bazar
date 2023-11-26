document.addEventListener('DOMContentLoaded', function (){
    // Your existing JavaScript code here
document.getElementById('submit_registration_button').addEventListener('click', validateForm)



// Předvyplnění formuláře s předchozími hodnotami
document.getElementById('username').value = localStorage.getItem('username') || '';

// Uložení hodnot do localStorage po změně
document.getElementById('username').addEventListener('input', function () {
    localStorage.setItem('username', document.getElementById('username').value);
});

});
function validateForm(event){
    let name = document.getElementById('username').value;
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirm-password').value;
    let agreed = document.getElementById('agreement_checkbox').value;
    let isValid = true;

    // Validace jména
    if (name === '') {
        isValid = false;
        document.getElementById('usernameError').innerHTML = 'Vyplňte jméno';
        document.getElementById("username").classList.add("error")
    } else {
        document.getElementById("username").classList.remove("error")
        document.getElementById('usernameError').innerHTML = '';
    }

    // Validace hesla
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

    if (!agreed){
         isValid = false;
        document.getElementById('agreement_checkbox').classList.add("error");
    }

    // Pokud je formulář neplatný, neodesílej ho
    if(!isValid){
        event.preventDefault();
    }

}