 document.addEventListener('DOMContentLoaded', (event) => {
    document.querySelector('form').addEventListener('submit', function(e) {
        var button = e.submitter;
        if (button.name === 'action' && button.value === 'delete') {
            if (!confirm('Opravdu chcete smazat tuto nabídku?')) {
                e.preventDefault();
            }
        }
    });
});

