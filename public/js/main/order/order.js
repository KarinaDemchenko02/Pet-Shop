import Form from "../../Form.js";

export class Order
{
	attachToNodeId = '';
	rootNode;
	id;
	title;
	price;
	imagePath;
	constructor({ attachToNodeId = '' , id, title, price, imagePath})
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

		this.id = id;
		this.title = title;
		this.price = price;
		this.imagePath = imagePath;
		this.rootNode = rootNode;
	}

	handleAddOrderButtonSubmit() {
		const name = document.getElementById('nameOrder').value;
		const surname = document.getElementById('surnameOrder').value;
		const address = document.getElementById('addressOrder').value;

		const form = document.getElementById('form-product');
		const buttonOrder = document.querySelector('.form-product__submit');
		buttonOrder.disabled = true;

		const orderParams = {
			id: this.id,
			name: name,
			surname: surname,
			address: address
		}
		fetch(
			`/product/${this.id}/`,
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(orderParams),
			}
		)
			.then((response) => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then((response) => {
				if (response.result) {
					const success = document.getElementById('success');
					buttonOrder.disabled = false;
					form.classList.remove('open');
					success.style.display = 'block';

					document.getElementById('nameOrder').value = '';
					document.getElementById('surnameOrder').value = '';
					document.getElementById('addressOrder').value = '';
				}
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
				buttonOrder.disabled = false;
			});
	}

	render()
	{
		this.rootNode.innerHTML = `
			<div class="form-product__container">
				<button class="form-product__close">
					<i class="form-product__close-icon material-icons">close</i>
				</button>
				<div class="form-product__modal">
					<div class="form-product__item">
						<img class="form-product__images" src="${this.imagePath}" alt="product">
						<h2 class="form-product__heading">${this.title}</h2>
						<p class="form-product__price">${this.price} ₽</p>
					</div>
					<div class="form-product__round"></div>
				</div>
				<div class="form-product__container-info">
					<div class="form-product__info">
					<h2 class="form-product__info-heading">Оформление заказа</h2>
					</div>
					<form class="form-product__main-form" id="form__order-add">
						<div class="form-product__container-input">
						<label class="form-product__label" for="name">Имя</label>
						<input class="form-product__input" id="nameOrder" name="name" type="text" required>
						</div>
						<div class="form-product__container-input">
						<label class="form-product__label" for="surname">Фамилия</label>
						<input class="form-product__input" id="surnameOrder" name="surname" type="text" required>
						</div>
						<div class="form-product__container-input">
						<label class="form-product__label" for="address">Адрес доставки</label>
						<input class="form-product__input" id="addressOrder" name="address" type="text" required>
						</div>
						<button class="form-product__submit" type="submit">Купить</button>
					</form>
				</div>
			</div>
			`;

		const spinner = document.createElement('div');
		spinner.classList.add('spinner-border', 'text-light', 'spinner-action', 'spinner-order');
		const spinnerLoading = document.createElement('span');
		spinnerLoading.innerText = 'Loading...';
		spinnerLoading.classList.add('visually-hidden');
		spinner.append(spinnerLoading);

		const buttonOrder = document.querySelector('.form-product__submit');
		buttonOrder.append(spinner);

		const form = document.getElementById('form__order-add');
		form.addEventListener('submit', function(event) {
			event.preventDefault();
			this.handleAddOrderButtonSubmit();
		}.bind(this));

		const buttonBuyOpen = document.querySelector('.details__buy');
		const buttonBuyClose = document.querySelector('.form-product__close');
		const formBuy = document.querySelector('.form-product');
		const objectBuyForm = new Form(buttonBuyOpen, buttonBuyClose, formBuy)
		objectBuyForm.open();
		objectBuyForm.close();
	}
}
