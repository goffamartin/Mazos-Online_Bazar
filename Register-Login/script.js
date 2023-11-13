function validateRegistrationForm() {
    var name = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm-password').value;
    var isValid = true;

    // Validace jména
    if (name === '') {
        isValid = false;
        document.getElementById('usernameError').innerHTML = 'Vyplňte jméno';
    } else {
        document.getElementById('usernameError').innerHTML = '';
    }

    // Validace hesla
    if (password === '') {
        isValid = false;
        document.getElementById('passwordError').innerHTML = 'Vyplňte heslo';
    } else {
        document.getElementById('passwordError').innerHTML = '';
    }

    if (confirmPassword !== password){
        isValid = false;
        document.getElementById('confirm-passwordError').innerHTML = 'Hesla se neshodují';
    } else {
        document.getElementById('confirm-passwordError').innerHTML = '';

    }

    // Pokud je formulář neplatný, neodesílej ho
    return isValid;
}

// Předvyplnění formuláře s předchozími hodnotami
document.getElementById('username').value = localStorage.getItem('username') || '';

// Uložení hodnot do localStorage po změně
document.getElementById('username').addEventListener('input', function () {
    localStorage.setItem('username', document.getElementById('username').value);
});
