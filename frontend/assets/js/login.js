document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('log-reg-form');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('/baumarkt-app/backend/api/login-handler.php', {
                method: 'POST',
                body: JSON.stringify({
                    username: formData.get('username'),
                    password: formData.get('password'),
                }),
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const result = await response.json();
            console.log('Server Response:', result);

            if (response.ok) {
                // Success feedback
                messageDiv.textContent = result.message;
                messageDiv.style.color = 'green';

                setTimeout(() => {
                    window.location.href = '/baumarkt-app/frontend/index.php';
                }, 1000);
            } else {
                // Error feedback
                messageDiv.textContent = result.message;
                messageDiv.style.color = 'red';
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            messageDiv.textContent = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            messageDiv.style.color = 'red';
        }
    });
});
