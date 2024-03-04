import {Error} from "../error/error.js";

export class Auth
{
	attachToNodeId = '';
	rootNode;
	login;

	constructor({ attachToNodeId = '' , login})
	{
		if (attachToNodeId === '')
		{
			new basketItem
			throw new Error('attachToNodeId must be a filled string.');
		}

		const rootNode = document.getElementById(attachToNodeId)
		if (!rootNode)
		{
			throw new Error(`There is no element with this ID: ${attachToNodeId}.`);
		}

		this.rootNode = rootNode;
		this.isLogin = login;
	}

	handleLogInButtonSubmit()
	{
		const form = document.querySelector('.form');
		let email = document.getElementById('email').value;
		let password = document.getElementById('password').value;

		const formContainer = document.querySelector('.form__main-container');
		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

		const logIn = document.getElementById('logIn');
		logIn.disabled = true;

		const authParams = {
			email: email,
			password: password,
			action: 'logIn'
		}
		fetch(
			'/logging/',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(authParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.result)
				{
					document.getElementById('email').value = '';
					document.getElementById('password').value = '';

					form.classList.remove('open');
					form.style.display = 'none';
					logIn.disabled = false;

					this.renderLogOut();
				}
				else
				{
					console.error(response.errors);
					logIn.disabled = false;

					new Error('Что-то пошло не так', formContainer).printError();
				}
			})
			.catch((error) => {
				console.error('Error while changing item.', error);
				logIn.disabled = false;
				new Error('Что-то пошло не так', formContainer).printError();
			})
	}

	handleLogOutButtonSubmit()
	{
		const authParams = {
			action: 'logOut'
		}

		fetch(
			'/logging/',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(authParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.result)
				{
					this.renderLogIn();
				}
				else
				{
					console.error(response.errors);
				}
			})
			.catch((error) => {
				console.error('Error while changing item.', error);
			})
	}

	handleRegisterButtonSubmit()
	{
		let name = document.getElementById('name').value;
		let surname = document.getElementById('surname').value;
		let phone = document.getElementById('phone').value;
		let email = document.getElementById('email').value;
		let password = document.getElementById('password').value;

		const form = document.querySelector('.form');
		const register = document.getElementById('register');
		register.disabled = true;

		const authParams = {
			action: 'register',
			name: name,
			surname: surname,
			phone: phone,
			email: email,
			password: password
		}
		fetch(
			'/logging/',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(authParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.result)
				{
					document.getElementById('name').value = '';
					document.getElementById('surname').value = '';
					document.getElementById('phone').value = '';
					document.getElementById('email').value = '';
					document.getElementById('password').value = '';

					form.classList.remove('open');
					form.style.display = 'none';

					register.disabled = false;
				}
				else
				{
					console.error(response.errors);
					register.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.', error);
				register.disabled = false;
			})
	}

	handleOpenForm() {
		const formAth = document.querySelector('.form');
		formAth.style.display = 'block';
	}

	handleCloseForm() {
		const formAth = document.querySelector('.form');
		formAth.style.display = 'none';
	}

	handleOpenMenu() {
		const menu = document.querySelector('.form__container-info');
		menu.style.right = '-134%';
	}

	handleCloseMenu() {
		const menu = document.querySelector('.form__container-info');
		menu.style.right = '-60%';
	}

	handleCreateAccount(event) {
		const buttonCreateAccount = event.target;
		const inputsRegister = document.querySelectorAll('.form__input-register');
		const buttonLogIn = document.querySelector('.form__button');
		const buttonRegister = document.querySelector('.form__button-register');

		let name = document.getElementById('name');
		let surname = document.getElementById('surname');
		let phone = document.getElementById('phone');
		let email = document.getElementById('email');
		let password = document.getElementById('password');

		name.value = '';
		surname.value = '';
		phone.value = '';
		email.value = '';
		password.value = '';

		if (buttonCreateAccount.textContent === 'Создать аккаунт') {
			inputsRegister.forEach(input => {
				input.style.display = 'block';
				buttonRegister.style.display = 'block';
				buttonLogIn.style.display = 'none';

				buttonCreateAccount.textContent = 'Войти';
			})
		} else {
			inputsRegister.forEach(input => {
				input.style.display = 'none';
				buttonRegister.style.display = 'none';
				buttonLogIn.style.display = 'block';

				buttonCreateAccount.textContent = 'Создать аккаунт';
			})
		}

	}

	render()
	{
		this.rootNode.innerHTML = `
			<div class="form__container">
				<div class="form__box">
					<div class="form__login-tab"></div>
					<div class="form__login-title">
						<button class="form__button-close">
							<i class="form__icon-login material-icons">close</i>
						</button>
					</div>
					<div class="form__login-container">
						<button class="form__open-menu">
							<svg width="20px" height="20px" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path clip-rule="evenodd" d="M5.7 12.1143C4.82423 12.1143 4.11429 12.8242 4.11429 13.7C4.11429 14.5758 4.82423 15.2857 5.7 15.2857C6.57577 15.2857 7.28571 14.5758 7.28571 13.7C7.28571 12.8242 6.57577 12.1143 5.7 12.1143ZM2 13.7C2 11.6565 3.65655 10 5.7 10C7.74345 10 9.4 11.6565 9.4 13.7C9.4 15.7435 7.74345 17.4 5.7 17.4C3.65655 17.4 2 15.7435 2 13.7Z" fill="#1c7430" fill-rule="evenodd"/>
								<path clip-rule="evenodd" d="M22.7 12.1143C21.8242 12.1143 21.1143 12.8242 21.1143 13.7C21.1143 14.5758 21.8242 15.2857 22.7 15.2857C23.5758 15.2857 24.2857 14.5758 24.2857 13.7C24.2857 12.8242 23.5758 12.1143 22.7 12.1143ZM19 13.7C19 11.6565 20.6565 10 22.7 10C24.7435 10 26.4 11.6565 26.4 13.7C26.4 15.7435 24.7435 17.4 22.7 17.4C20.6565 17.4 19 15.7435 19 13.7Z" fill="#1c7430" fill-rule="evenodd"/>
								<path clip-rule="evenodd" d="M14.2 12.1143C13.3242 12.1143 12.6143 12.8242 12.6143 13.7C12.6143 14.5758 13.3242 15.2857 14.2 15.2857C15.0758 15.2857 15.7857 14.5758 15.7857 13.7C15.7857 12.8242 15.0758 12.1143 14.2 12.1143ZM10.5 13.7C10.5 11.6565 12.1565 10 14.2 10C16.2435 10 17.9 11.6565 17.9 13.7C17.9 15.7435 16.2435 17.4 14.2 17.4C12.1565 17.4 10.5 15.7435 10.5 13.7Z" fill="#1c7430" fill-rule="evenodd"/>
							</svg>
						</button>
						<form class="form__main-container" method="post" action="/logging/">
							<div class="form__input-container">
								<div class="form__input-container form__container-name form__input-register">
									<label class="form__label" for="name">ИМЯ</label>
									<input class="form__input" type="text" id="name" name="name" required>
								</div>
								<div class="form__input-container form__container-surname form__input-register">
									<label class="form__label" for="surname">ФАМИЛИЯ</label>
									<input class="form__input" type="text" id="surname" name="surname" required>
								</div>
								<div class="form__input-container form__container-phone form__input-register">
									<label class="form__label" for="phone">Телефон</label>
									<input class="form__input" type="text" id="phone" name="phone" required>
								</div>
								<div class="form__input-container form__container-email">
									<label class="form__label" for="email">E-MAIL</label>
									<input class="form__input" type="text" id="email" name="email" required>
								</div>
								<div class=" form__input-container form__container-password">
									<label class="form__label" for="password">ПАРОЛЬ</label>
									<input class="form__input" type="password" id="password" name="password" required>
								</div>
							</div>
							<button class="form__button" id="logIn" type="submit" name="logIn">ВОЙТИ</button>
							<button class="form__button form__button-register" id="register" type="submit" name="register">ЗАРЕГИСТРИРОВАТЬСЯ</button>
						</form>
					</div>
				</div>
				<div class="form__container-info">
					<div class="form__container-help">
						<button class="form__info-close">
							<i class="form__icon-close material-icons">close</i>
						</button>
						<h3 class="form__info-heading">Нужна помощь?</h3>
					</div>
					<a href="#footer" class="form__button-menu form__button-support">Связаться с нами</a>
					<a class="form__button-menu form__create-account">Создать аккаунт</a>
				</div>
			</div>
			`;

		const formClose = document.querySelector('.form__button-close');
		formClose.addEventListener('click', this.handleCloseForm.bind(this));

		const openMenu = document.querySelector('.form__open-menu');
		openMenu.addEventListener('click', this.handleOpenMenu.bind(this));

		const closeMenu = document.querySelector('.form__info-close');
		closeMenu.addEventListener('click', this.handleCloseMenu.bind(this));

		const createAccount = document.querySelector('.form__create-account');
		createAccount.addEventListener('click', this.handleCreateAccount.bind(this));

		const spinner = document.createElement('div');
		spinner.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoading = document.createElement('span');
		spinnerLoading.innerText = 'Loading...';
		spinnerLoading.classList.add('visually-hidden');
		spinner.append(spinnerLoading);

		const spinnerRegister = document.createElement('div');
		spinnerRegister.classList.add('spinner-border', 'text-light', 'spinner-action', 'spinner-register');
		const spinnerRegisterLoading = document.createElement('span');
		spinnerRegisterLoading.innerText = 'Loading...';
		spinnerRegisterLoading.classList.add('visually-hidden');
		spinnerRegister.append(spinnerRegisterLoading);

		const logIn = document.getElementById('logIn');
		logIn.addEventListener('click', function(event) {
			event.preventDefault();
			this.handleLogInButtonSubmit();
		}.bind(this));

		const register = document.getElementById('register');
		register.addEventListener('click', function(event) {
			event.preventDefault();
			this.handleRegisterButtonSubmit();
		}.bind(this));

		if (this.isLogin === '1') {
			this.renderLogOut();
		} else {
			this.renderLogIn();
		}

		logIn.append(spinner);
		register.append(spinnerRegister);
	}

	renderLogOut()
	{
		const containerAuth = document.getElementById('header__auth');
		containerAuth.innerHTML = '';
		containerAuth.innerHTML =  `
			<form method="post" action="/logging/">
				<button name="logOut" id="logOut" type="submit" class="header__come-out">
					<svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M2.00098 11.999L16.001 11.999M16.001 11.999L12.501 8.99902M16.001 11.999L12.501 14.999"
						stroke="#ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path
						d="M9.00195 7C9.01406 4.82497 9.11051 3.64706 9.87889 2.87868C10.7576 2 12.1718 2 15.0002 2L16.0002 2C18.8286 2 20.2429 2 21.1215 2.87868C22.0002 3.75736 22.0002 5.17157 22.0002 8L22.0002 16C22.0002 18.8284 22.0002 20.2426 21.1215 21.1213C20.3531 21.8897 19.1752 21.9862 17 21.9983M9.00195 17C9.01406 19.175 9.11051 20.3529 9.87889 21.1213C10.5202 21.7626 11.4467 21.9359 13 21.9827"
						stroke="#ffff" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
					<span class="product__come-in-name">Выйти</span>
				</button>
			</form>
		`;

		const logOut = document.getElementById('logOut');

		logOut.addEventListener('click', function(event) {
			event.preventDefault();
			this.handleLogOutButtonSubmit();
		}.bind(this));
	}

	renderLogIn()
	{
		const containerAuth = document.getElementById('header__auth');
		containerAuth.innerHTML = '';
		containerAuth.innerHTML =  `
			<button class="header__come-in">
				<svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2.00098 11.999L16.001 11.999M16.001 11.999L12.501 8.99902M16.001 11.999L12.501 14.999"
					stroke="#ffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path
					d="M9.00195 7C9.01406 4.82497 9.11051 3.64706 9.87889 2.87868C10.7576 2 12.1718 2 15.0002 2L16.0002 2C18.8286 2 20.2429 2 21.1215 2.87868C22.0002 3.75736 22.0002 5.17157 22.0002 8L22.0002 16C22.0002 18.8284 22.0002 20.2426 21.1215 21.1213C20.3531 21.8897 19.1752 21.9862 17 21.9983M9.00195 17C9.01406 19.175 9.11051 20.3529 9.87889 21.1213C10.5202 21.7626 11.4467 21.9359 13 21.9827"
					stroke="#ffff" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
				<span class="product__come-in-name">Войти</span>
			</button>
		`;

		const headerLogIn = document.querySelector('.header__come-in');
		headerLogIn.addEventListener('click', this.handleOpenForm)
	}
}
