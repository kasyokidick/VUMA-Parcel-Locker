/**
 * Authentication JavaScript
 * Handles login form submission and user session management
 */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const logoutButtons = document.querySelectorAll('[data-logout]');
    logoutButtons.forEach(button => {
        button.addEventListener('click', handleLogout);
    });
});

async function handleLogin(e) {
    e.preventDefault();

    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const spinner = submitButton.querySelector('.spinner-border');
    const buttonText = submitButton.querySelector('span:not(.visually-hidden)');

    const formData = new FormData(form);
    const loginData = {
        email: formData.get('email'),
        password: formData.get('password')
    };

    if (!loginData.email || !loginData.password) {
        showAlert('Please enter both email and password', 'danger');
        return;
    }

    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Logging in...';

    try {
        const response = await fetch('api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(loginData)
        });

        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success');
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1000);
        } else {
            showAlert(result.error, 'danger');
            form.classList.add('shake');
            setTimeout(() => {
                form.classList.remove('shake');
            }, 500);
        }

    } catch (error) {
        console.error('Login error:', error);
        showAlert('Network error. Please check your connection and try again.', 'danger');
    } finally {
        submitButton.disabled = false;
        spinner.classList.add('d-none');
        buttonText.textContent = 'Login';
    }
}

async function handleLogout(e) {
    e.preventDefault();

    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    try {
        const response = await fetch('api/auth/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            window.location.href = 'login.php?logout=success';
        }

    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.php?logout=success';
    }
}

function showAlert(message, type = 'info') {
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    const alertContainer = document.querySelector('.container') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';

    let icon = '';
    switch (type) {
        case 'success':
            icon = '<i class="bi bi-check-circle-fill me-2"></i>';
            break;
        case 'danger':
            icon = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
            break;
        default:
            icon = '<i class="bi bi-info-circle-fill me-2"></i>';
    }

    alert.innerHTML = `
        ${icon}${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    if (alertContainer.firstChild) {
        alertContainer.insertBefore(alert, alertContainer.firstChild);
    } else {
        alertContainer.appendChild(alert);
    }

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}