import AddBasket from './AddBasket.js';
import Tabs from './Tabs.js';
import Form from "./Form.js";

const buttonBuy = document.querySelectorAll('.product__buy');
const buttonRemove = document.querySelectorAll('.product__right-remove');
new AddBasket(buttonBuy, buttonRemove, 'product__bottom-content').addBasket();

const buttonBasketOpen = document.getElementById('buttonBasket');
const buttonBasketClose = document.querySelector('.basket__button-close');
const formBasket = document.querySelector('.basket');
const objectFormBasket = new Form(buttonBasketOpen, buttonBasketClose, formBasket);
objectFormBasket.open();
objectFormBasket.close();


if (document.getElementById("img-container")) {
	let options = {
		width: 400,
		height: 400,
	};

	new ImageZoom(document.getElementById("img-container"), options);
}

// const buttonSupport = document.querySelector('.form__button-support');
// buttonSupport.addEventListener('click', () => {
// 	document.querySelector('.body').classList.remove('no-scroll');
// 	document.querySelector('.form').classList.remove('open');
// })

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

const btnsItem = document.querySelectorAll('.details__btn-item');
const blocksWithInfo = document.querySelectorAll('.details__info-items');
new Tabs(btnsItem, blocksWithInfo).tabs();




