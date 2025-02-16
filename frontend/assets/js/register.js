document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const messageDiv = document.getElementById('message');

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(registerForm);

        try {
            const response = await fetch('/baumarkt-app/backend/api/api-register.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (response.ok) {
                // Success feedback
                messageDiv.textContent = result.message;
                messageDiv.style.color = 'green';

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '/baumarkt-app/frontend/login-page.php';
                }, 2000); // 2-second delay
            } else {
                // Error feedback
                messageDiv.textContent = result.message;
                messageDiv.style.color = 'red';
            }
        } catch (error) {
            // Handle unexpected errors
            console.error('Error:', error);
            messageDiv.textContent = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            messageDiv.style.color = 'red';
        }
    });
});