import Swal from 'sweetalert2';

window.Swal = Swal;
window.Toast = Swal.mixin({
	toast: true,
	position: 'top-end',
	width: 'min(90vw, 420px)',
	showConfirmButton: false,
	timer: 3500,
	timerProgressBar: true,
});

document.addEventListener('DOMContentLoaded', function () {
	const loginForm = document.querySelector('[data-login-form]');

	if (!loginForm) {
		return;
	}

	const loginError = loginForm.querySelector('[data-login-error]');

	loginForm.addEventListener('submit', async function (event) {
		event.preventDefault();
		loginError.hidden = true;
		loginError.textContent = '';

		const submitButton = loginForm.querySelector('button[type="submit"]');
		submitButton.disabled = true;

		try {
			const response = await fetch(loginForm.action, {
				method: 'POST',
				body: new FormData(loginForm),
				headers: {
					Accept: 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
			});
			const data = await response.json();

			if (!response.ok) {
				throw new Error(data.message || 'Unable to sign in.');
			}

			Toast.fire({
				icon: 'success',
				title: data.message,
				background: '#a5dc86',
				iconColor: '#1f4b39',
			});

			window.setTimeout(function () {
				window.location.assign(data.redirect);
			}, 400);
		} catch (error) {
			loginError.textContent = error.message;
			loginError.hidden = false;

			Toast.fire({
				icon: 'error',
				title: error.message,
				background: '#f27474',
				iconColor: '#b94f3c',
			});
		} finally {
			submitButton.disabled = false;
		}
	});
});
