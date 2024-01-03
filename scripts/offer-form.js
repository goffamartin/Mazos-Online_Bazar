 document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelector('form').addEventListener('submit', function(e) {
        var button = e.submitter;
        if (button.name === 'action' && button.value === 'delete') {
            if (!confirm('Opravdu chcete smazat tuto nabídku?'))
                e.preventDefault();
        }
        if(button.name === 'action' && button.value === 'save')
            validateOffer(e);
    });
});

function validateOffer(event){
    let offerId;
    try{
        offerId = document.getElementById('offerId').value;
    } catch {
        offerId = "";
    }
    let title = document.getElementById('title').value;
    let price = document.getElementById('price').value;
    let image = document.getElementById('image').files;
    let isValid = true;

    if (title === "") {
        isValid = false;
        document.getElementById('titleError').innerHTML = 'Vyplňte titulek nabídky';
        document.getElementById("title").classList.add("error")
    } else {
        document.getElementById("title").classList.remove("error")
        document.getElementById('titleError').innerHTML = '';
    }

    if (image.length === 0 && offerId === ""){
        isValid = false;
        document.getElementById('imageError').innerHTML = 'Nahrajte obrázek';
    } else {
        document.getElementById('imageError').innerHTML = '';
    }

    if (price === "") {

        isValid = false;
        document.getElementById('priceError').innerHTML = 'Vyplňte cenu';
        document.getElementById("price").classList.add("error");

    } else {
        if(price >= 2147483647 || price < 0){
            document.getElementById('priceError').innerHTML = 'min. 0 kč max. 2147483647 kč';
            document.getElementById("price").classList.add("error");
        } else{
            document.getElementById('priceError').innerHTML = '';
            document.getElementById("price").classList.remove("error");
        }
    }
    if (!isValid) {
        event.preventDefault();
    }

}

