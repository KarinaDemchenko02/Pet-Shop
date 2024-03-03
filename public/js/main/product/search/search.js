import {Error} from "../../error/error.js";
import {ProductItem} from "../product-item.js";
import {ProductList} from "../product-list.js";
import {BasketItem} from "../../basket/basket-item.js";

export class Search
{
	attachToNodeId = '';
	rootNode;
	items = [];
	basketItem = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');

	constructor({ attachToNodeId = '', items, basketItem })
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

		this.items = items.map((itemData) => {
			return this.createItem(itemData)
		})

		this.basketItem = basketItem.map((itemData) => {
			return this.createBasket(itemData);
		})
	}

	createItem(itemData)
	{
		return new ProductItem(itemData);
	}

	createBasket(itemData)
	{
		return new BasketItem(itemData)
	}

	handleSearchButtonSubmit()
	{
		const inputSearch = document.getElementById('search');

		const title = inputSearch.value;

		let currentUrl = window.location.href;

		let page = '1';

		this.currentPagination = '1';

		let newUrl = new URL(currentUrl);

		newUrl.searchParams.set('title', title);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		const spinner = document.querySelector('.spinner-product');

		if (spinner) {
			spinner.classList.add('disabled');
		}

		fetch(
			`/search-json/?title=${title}&page=${page}`,
			{
				method: 'GET',
			}
		)
			.then((response) => {
				if (response.status >= 300 || response.status < 200) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(async (response) => {
				if (response.nextPage.length !== 0) {
					this.currentPagination = Number(page) + 1;
				}

				let products = response.products.map((itemData) => {
					return this.createItem(itemData);
				});

				const productsList = new ProductList({
					attachToNodeId: 'product__list-container',
					items: products,
					basketItem: this.basketItem
				});

				await productsList.render();

				this.renderPagination();

				if (spinner) {
					spinner.classList.remove('disabled');
				}
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
				if (spinner) {
					spinner.classList.remove('disabled');
				}
			});
	}

	handlePaginationButtonClick(event)
	{
		if (this.items)
		{
			const page = event.target.innerText;
			const title = new URLSearchParams(window.location.search).get('title');

			let currentUrl = window.location.href;

			let newUrl = new URL(currentUrl);
			newUrl.searchParams.set('title', title);
			newUrl.searchParams.set('page', page);

			window.history.replaceState({}, '', newUrl);

			fetch(
				`/tags-json/?title=${title}&page=${page}`,
				{
					method: 'GET',
				}
			)
				.then((response) => {
					return response.json();
				})
				.then(async (response) => {
					if (response.nextPage.length !== 0) {
						this.currentPagination = Number(page) + 1;
					}

					let products = response.products.map((itemData) => {
						return this.createItem(itemData);
					});

					const productsList = new ProductList({
						attachToNodeId: 'product__list-container',
						items: products,
						basketItem: this.basketItem
					});

					await productsList.render();

					this.renderPagination();

					const spinner = document.querySelector('.spinner-product');
					spinner.classList.remove('disabled');
				})
				.catch((error) => {
					console.error('Error while deleting item.', error);
					const spinner = document.querySelector('.spinner-product');
					spinner.classList.remove('disabled');
				})
		}
	}

	render()
	{
		this.rootNode.innerHTML = '';

		const form = document.createElement('form');
		form.classList.add('header__main-form');

		form.addEventListener('submit', function(event) {
			event.preventDefault();
			this.handleSearchButtonSubmit();
		}.bind(this));

		const labelSearch = document.createElement('label');
		labelSearch.classList.add('header__label');

		const inputSearch = document.createElement('input');
		inputSearch.classList.add('header__input');
		inputSearch.name = 'title';
		inputSearch.id = 'search'
		inputSearch.placeholder = 'Поиск товаров';

		if (new URLSearchParams(window.location.search).get('title'))
		{
			inputSearch.value = new URLSearchParams(window.location.search).get('title');
		}

		const searchButton = document.createElement('button');
		searchButton.classList.add('header__button')
		searchButton.type = 'submit';

		const buttonIcon = document.createElement('i');
		buttonIcon.classList.add('header__search', 'material-icons');
		buttonIcon.innerText = 'search';

		searchButton.append(buttonIcon)

		labelSearch.append(inputSearch);
		form.append(labelSearch, searchButton);

		this.rootNode.append(form);
	}

	renderPagination()
	{
		const containerPagination = document.getElementById('buttonPagination');

		containerPagination.innerHTML = '';

		let currentPage = parseInt(new URLSearchParams(window.location.search).get('page') || '1');
		const startIndex = Math.max(1, currentPage - 1);
		const endIndex = Math.min(parseInt(this.currentPagination), currentPage + 1);

		for (let i = startIndex; i <= endIndex; i++) {
			const buttonPagination = document.createElement('button');
			buttonPagination.classList.add('pagination__button');
			buttonPagination.innerText = String(i);

			let currentPage = new URLSearchParams(window.location.search).get('page');

			if (buttonPagination.innerText === currentPage) {
				buttonPagination.classList.add('is-active');
			}

			buttonPagination.addEventListener('click', this.handlePaginationButtonClick.bind(this));

			containerPagination.append(buttonPagination);
		}
	}
}
