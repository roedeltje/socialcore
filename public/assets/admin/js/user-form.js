document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.admin-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            let isValid = true;
            let errorMessages = [];
            
            // Reset eerdere foutmeldingen
            const existingAlert = document.querySelector('.alert-danger');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Gebruikersnaam validatie
            if (username.length < 3) {
                isValid = false;
                errorMessages.push("Gebruikersnaam moet minimaal 3 tekens bevatten.");
            }
            
            // Email validatie
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                isValid = false;
                errorMessages.push("Voer een geldig e-mailadres in.");
            }
            
            // Wachtwoord validatie
            if (password.length < 8) {
                isValid = false;
                errorMessages.push("Wachtwoord moet minimaal 8 tekens bevatten.");
            }
            
            // Wachtwoord bevestiging
            if (password !== passwordConfirm) {
                isValid = false;
                errorMessages.push("Wachtwoorden komen niet overeen.");
            }
            
            // Als er fouten zijn, stop het verzenden en toon foutmeldingen
            if (!isValid) {
                e.preventDefault();
                
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = errorMessages.join('<br>');
                
                const cardBody = form.closest('.card-body');
                cardBody.insertBefore(alertDiv, form);
                
                // Scroll naar de foutmelding
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
});