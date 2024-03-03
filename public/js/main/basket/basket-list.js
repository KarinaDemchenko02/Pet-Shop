import {BasketItem} from "./basket-item.js";

export class BasketList
{
	attachToNodeId = '';
	rootNode;
	items = [];

	constructor({ attachToNodeId = '', items })
	{
		if (attachToNodeId === '')
		{
			throw new Error('attachToNodeId must be a filled string.');
		}

		const rootNode = document.getElementById(attachToNodeId)
		if (!rootNode)
		{
			throw new Error(`There is no element with this ID: ${attachToNodeId}.`);
		}

		this.rootNode = rootNode;
		this.items = items.map((itemData) => {
			return this.createItem(itemData)
		})

	}
	createItem(itemData)
	{
		return new BasketItem(itemData);
	}

	handleRemoveButtonClick(item)
	{
		const numberPattern = /\d+/g;
		const itemId = item.target.parentNode.parentNode.id;
		const id = itemId.match(numberPattern);

		if (id > -1)
		{
			fetch(
				`/deleteFromBasket/${id}/`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result)
					{
						const itemToRemove = document.getElementById(`itemBasket${id}`);
						if (itemToRemove) {
							itemToRemove.remove();
							const bottomMenu = document.getElementById(`bottom${id}`);
							const counterOrder = document.querySelector('.header__basket-number');
							counterOrder.innerText = Number(counterOrder.innerText) - 1;
							bottomMenu.classList.remove('clicked');
						}
					}
					else
					{
						console.error('Error while disabling item.');
					}
				})
				.catch((error) => {
					console.error('Error while disabling item.', error);
				})
		}
	}

	openFormBuyProduct()
	{
		this.renderFormBuy();

		document.getElementById('basket__buyProduct').style.display = 'block';

		const formBuyOrder = document.getElementById('form__order-add');
		const formClose = document.querySelector('.form-product__close');

		formClose.addEventListener('click', () => {
			document.getElementById('basket__buyProduct').style.display = 'none';
		})

		const spinner = document.createElement('div');
		spinner.classList.add('spinner-border', 'text-light', 'spinner-action', 'spinner-add-products');
		const spanSpinner = document.createElement('span');
		spanSpinner.classList.add('visually-hidden');
		spanSpinner.innerText = 'Loading...';
		spinner.append(spanSpinner);

		const buyProducts = document.querySelector('.form-product__submit');
		buyProducts.append(spinner)

		formBuyOrder.addEventListener('submit', function(event) {
			event.preventDefault();
			this.handleBuyBasketButtonClick();
		}.bind(this));
	}

	handleBuyBasketButtonClick()
	{
		const buyProducts = document.querySelector('.form-product__submit');
		const name = document.getElementById('nameOrder').value;
		const surname = document.getElementById('surnameOrder').value;
		const address = document.getElementById('addressOrder').value;

		const basketParams = {
			name: name,
			surname: surname,
			address: address
		};

		buyProducts.disabled = true;

		fetch(
			`/createOrder/`,
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(basketParams)
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.result)
				{
					document.getElementById('basket__buyProduct').style.display = 'none';
					document.querySelector('.basket').classList.remove('open');
					document.querySelector('.header__basket-number').innerText = '0';
					document.getElementById('basket-list').innerHTML = '';

					document.querySelectorAll('.product__bottom-content').forEach(content => {
						content.classList.remove('clicked');
					})
				}
				else
				{
					console.error('Error while disabling item.');
				}
			})
			.catch((error) => {
				console.error('Error while disabling item.', error);
			})
	}
	render()
	{
		this.rootNode.innerHTML = '';

		let itemsHtml = '';

		this.items.forEach((item) => {
			itemsHtml += item.render();
		});

		this.rootNode.innerHTML = itemsHtml;

		const buttonDelete = document.querySelectorAll('.basket__delete');
		buttonDelete.forEach(btn => {
			btn.addEventListener('click', this.handleRemoveButtonClick.bind(this))
		});

		const buttonBuy = document.querySelector('.basket__buy');
		buttonBuy.addEventListener('click', this.openFormBuyProduct.bind(this));
	}

	renderFormBuy()
	{
		document.getElementById('basket__buyProduct').innerHTML = `
			<div class="form-product__container">
				<button class="form-product__close">
					<i class="form-product__close-icon material-icons">close</i>
				</button>
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
	}
}