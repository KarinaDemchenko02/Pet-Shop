import {ProductItem} from "./product-item.js";
import AddBasket from "../../AddBasket.js";
import {BasketItem} from "../basket/basket-item.js";
import {Error} from '../error/error.js';

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	basketItem = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');
	nextPage = [];

	constructor({ attachToNodeId = '', items, basketItem })
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

		this.basketItem = basketItem.map((itemData) => {
			return this.createBasket(itemData);
		})

		this.createItemsContainer();
	}

	createItem(itemData)
	{
		return new ProductItem(itemData);
	}

	createBasket(itemData)
	{
		return new BasketItem(itemData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('ul')
		this.itemsContainer.classList.add('product__list');

		this.rootNode.append(this.itemsContainer);
	}

	handleChangePaginationButtonClick(event) {
		event.preventDefault();
		const page = event.target.innerText;

		let currentUrl = window.location.href;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		const spinner = document.querySelector('.spinner-product');
		spinner.classList.add('disabled');

		fetch(
			`/products-json/?page=${page}`,
			{
				method: 'GET',
			}
		)
		.then((response) => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then((response) => {
			if (response.nextPage.length !== 0) {
				this.currentPagination = Number(page) + 1;
			}

			this.nextPage = response.nextPage;

			this.items = response.products.map((itemData) => {
				return this.createItem(itemData)
			})

			this.render();

			spinner.classList.remove('disabled');
		})
		.catch((error) => {
			console.error('Error while changing item:', error);
			spinner.classList.remove('disabled');
		});
	}

	handleBasketAddButtonSubmit(item) {
		const numberPattern = /\d+/g;
		const itemId = item.target.parentNode.parentNode.id;
		const id = itemId.match(numberPattern);

		fetch(
			`/addToBasket/${id}/`,
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
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
					const counterOrder = document.querySelector('.header__basket-number');
					counterOrder.innerText = Number(counterOrder.innerText) + 1;

					this.items.forEach(item => {
						if (item.id === Number(id)) {
							const basketContainer = document.getElementById('basket-list');
							basketContainer.innerHTML += new BasketItem(item).render();

							this.basketItem.push(new BasketItem(item));

							const buttonDelete = document.querySelectorAll('.basket__delete');
							buttonDelete.forEach(btn => {
								btn.addEventListener('click', this.handleBasketRemoveButtonSubmit.bind(this));
							})
							return true;
						}
					})
				}
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	handleBasketRemoveButtonSubmit(item)
	{
		const numberPattern = /\d+/g;
		const itemId = item.target.parentNode.parentNode.id;
		const id = itemId.match(numberPattern);

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
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then((response) => {
				if (response.result) {
					const counterOrder = document.querySelector('.header__basket-number');
					counterOrder.innerText = Number(counterOrder.innerText) - 1;

					this.items.forEach(item => {
						if (item.id === Number(id)) {
							const basketItem = document.getElementById(`itemBasket${id}`);
							basketItem.remove();

							this.basketItem.forEach((item, index) => {
								if (item.id === Number(id)) {
									this.basketItem.splice(index, 1);
									return true;
								}
							});

							const bottomMenu = document.getElementById(`bottom${id}`)
							bottomMenu.classList.remove('clicked');
							return true;
						}
					})
				}
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	async render() {
		if (this.items.length === 0) {
			const modal = new Error('Данная страница не найдена!').render();
			this.rootNode.append(modal);
			return false;
		}

		this.rootNode.innerHTML = '';

		let itemsHtml = '';

		this.items.forEach((item) => {
			itemsHtml += item.render();
		});

		const spinner = document.createElement('div');
		spinner.classList.add('spinner-border', 'text-success', 'spinner-product');
		const spanSpinner = document.createElement('span');
		spanSpinner.classList.add('visually-hidden');
		spanSpinner.innerText = 'Loading...';
		spinner.append(spanSpinner);

		this.itemsContainer.innerHTML = itemsHtml;
		this.itemsContainer.append(spinner);
		this.rootNode.append(this.itemsContainer);

		const buttonBuy = document.querySelectorAll('.product__buy');
		const buttonRemove = document.querySelectorAll('.product__right-remove');
		new AddBasket(buttonBuy, buttonRemove, 'product__bottom-content').addBasket();

		this.items.forEach((item) => {
			const id = item.id;
			this.basketItem.forEach(basket => {
				if (basket.id === id) {
					const bottomMenu = document.getElementById(`bottom${id}`)
					bottomMenu.classList.add('clicked');
					return true;
				}
			})
		});

		buttonBuy.forEach(btn => {
			btn.addEventListener('click', this.handleBasketAddButtonSubmit.bind(this))
		});

		buttonRemove.forEach(btn => {
			btn.addEventListener('click', this.handleBasketRemoveButtonSubmit.bind(this))
		});

		await this.renderPagination();
	}

	async renderPagination() {
		const paginationContainer = document.createElement('div');
		paginationContainer.id = 'buttonPagination'
		paginationContainer.classList.add('pagination');

		let current = 0;

		if (!this.currentPagination) {
			const result = await this.checkPageNumberOne();

			if (result) {
				current = 1;
			}

			this.currentPagination = 1;
		}

		if (this.currentPagination === '1') {
			const result = await this.checkPageNumberOne();

			if (result) {
				current = 1;
			}
		}

		let currentPage = parseInt(new URLSearchParams(window.location.search).get('page') || '1');
		const startIndex = Math.max(1, currentPage - 1);
		const endIndex = Math.min(parseInt(this.currentPagination), currentPage + 1);

		for (let i = startIndex; i <= endIndex + current; i++) {
			const buttonPagination = document.createElement('button');
			buttonPagination.classList.add('pagination__button');
			buttonPagination.innerText = String(i);
			buttonPagination.addEventListener('click', this.handleChangePaginationButtonClick.bind(this));

			if (currentPage === null) {
				currentPage = '1';
			}
			if (buttonPagination.innerText === String(currentPage)) {
				buttonPagination.classList.add('is-active');
			}

			paginationContainer.append(buttonPagination)

			this.rootNode.append(paginationContainer);
		}
	}

	checkPageNumberOne()
	{
		return fetch(
			`/products-json/?page=1`,
			{
				method: 'GET',
			}
		)
		.then((response) => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then((response) => {
			return response.nextPage.length !== 0;
		})
		.catch((error) => {
			console.error('Error while checking for items on next page:', error);
		});
	}
}
