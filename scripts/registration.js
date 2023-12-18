document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('submit-registration-button').addEventListener('click', validateRegistration)
    document.getElementById('username').addEventListener('focusout', checkUsernameAvailability)
});


function validateRegistration(event) {
    let username = document.getElementById('username').value;
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirm-password').value;
    let agreed = document.getElementById('agreement-checkbox');

    let isValid = true;

    if (username === '') {
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

    if (confirmPassword !== password) {
        isValid = false;
        document.getElementById('confirm-passwordError').innerHTML = 'Hesla se neshodují';
        document.getElementById("confirm-password").classList.add("error");
    } else {
        document.getElementById('confirm-passwordError').innerHTML = '';
        document.getElementById("confirm-password").classList.remove("error");
    }

    if (!agreed.checked) {
        isValid = false;
        document.getElementById('genericError').innerHTML = 'Pro registraci je nutné souhlasit s podmínkami';
    } else {
        document.getElementById('genericError').innerHTML = '';
    }

    if (!isValid) {
        event.preventDefault();
    }
}

async function checkUsernameAvailability() {
    const response = await fetch('../php/check_username.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'username=' + encodeURIComponent(document.getElementById('username').value)
    });
    const text = await response.text();
    // Check username availability
    const isAvailable = text === 'available';
    if (!isAvailable) {
        document.getElementById('usernameError').innerHTML = 'Toto jméno už je zabrané';
        document.getElementById('username').classList.add('error');
    } else {
        document.getElementById('username').classList.remove('error');
        document.getElementById('usernameError').innerHTML = '';
    }
}