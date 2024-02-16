import AddBasket from './AddBasket.js';
import Tabs from './Tabs.js';
import Form from "./Form.js";
import pagination from "./pagination.js";

pagination();

if (document.querySelector('.details__buy')) {
	const buttonBuyOpen = document.querySelector('.details__buy');
	const buttonBuyClose = document.querySelector('.form-product__close');
	const formBuy = document.querySelector('.form-product');
	const objectBuyForm = new Form(buttonBuyOpen, buttonBuyClose, formBuy)
	objectBuyForm.open();
	objectBuyForm.close();
}


const buttonBuy = document.querySelectorAll('.product__buy');
const buttonRemove = document.querySelectorAll('.product__right-remove');
new AddBasket(buttonBuy, buttonRemove, 'product__bottom-content').addBasket();

if (document.getElementById("img-container")) {
	let options = {
		width: 400,
		height: 400,
	};

	new ImageZoom(document.getElementById("img-container"), options);
}

if (document.querySelector('.form')) {
	const buttonLogin = document.querySelector('.header__come-in');
	const formAth = document.querySelector('.form');
	const buttonCloseForm = document.querySelector('.form__login-title')
	const objectForm = new Form(buttonLogin, buttonCloseForm, formAth);
	objectForm.open();
	objectForm.close();
}


if (document.querySelector('.form__open-menu')) {
	const buttonMenuOpen = document.querySelector('.form__open-menu');
	const menu = document.querySelector('.form__container-info');
	const buttonMenuClose = document.querySelector('.form__info-close');

	const objectMenuForm = new Form(buttonMenuOpen, buttonMenuClose, menu);
	objectMenuForm.openRight();
	objectMenuForm.closeRight();

	const buttonSupport = document.querySelector('.form__button-support');
	buttonSupport.addEventListener('click', () => {
		document.querySelector('.body').classList.remove('no-scroll');
		document.querySelector('.form').classList.remove('open');
	})

	const buttonCreateAccount = document.querySelector('.form__create-account');
	const inputsRegister = document.querySelectorAll('.form__input-register');
	const buttonLogIn = document.querySelector('.form__button');
	const buttonRegister = document.querySelector('.form__button-register');
	let isRegister = false;
	buttonCreateAccount.addEventListener('click', () => {
		if (!isRegister) {
			inputsRegister.forEach(input => {
				input.style.display = 'block';
				buttonRegister.style.display = 'block';
				buttonLogIn.style.display = 'none';

				buttonCreateAccount.textContent = 'Войти';

				isRegister = true;
			})
		} else {
			inputsRegister.forEach(input => {
				input.style.display = 'none';
				buttonRegister.style.display = 'none';
				buttonLogIn.style.display = 'block';

				buttonCreateAccount.textContent = 'Создать аккаунт';

				isRegister = false;
			})
		}

	})
}

const btnsItem = document.querySelectorAll('.details__btn-item');
const blocksWithInfo = document.querySelectorAll('.details__info-items');
new Tabs(btnsItem, blocksWithInfo).tabs();

const buttonPlus = document.querySelectorAll('.basket__plus-btn');
const buttonMinus = document.querySelectorAll('.basket__minus-btn');
buttonPlus.forEach(btn => {
	btn.addEventListener('click', (event) => {
		let parent = event.target.parentNode.parentNode;
		let inputNumber = parent.querySelector('.basket__input-number');

		let number = parseInt(inputNumber.value);
		number++;

		inputNumber.value = number;

	})
})

buttonMinus.forEach(btn => {
	btn.addEventListener('click', (event) => {
		let parent = event.target.parentNode.parentNode;
		let inputNumber = parent.querySelector('.basket__input-number');

		let number = parseInt(inputNumber.value);

		if (number !== 1) {
			number--;
		}
		inputNumber.value = number;

	})
})

const buttonBasketOpen = document.getElementById('buttonBasket');
const buttonBasketClose = document.querySelector('.basket__button-close');
const formBasket = document.querySelector('.basket');
const objectFormBasket = new Form(buttonBasketOpen, buttonBasketClose, formBasket);
objectFormBasket.open();
objectFormBasket.close();