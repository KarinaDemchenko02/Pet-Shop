import {ProductItem} from "./product-item.js";
import AddBasket from "../../AddBasket.js";
import {BasketItem} from "../basket/basket-item.js";

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	basketItem = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');

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

	handlePrevPaginationButtonClick() {
		if (parseInt(this.currentPagination) === 1) {
			return;
		}

		this.currentPagination = new URLSearchParams(window.location.search).get('page');

		let prevPageNumber = parseInt(this.currentPagination) - 1;

		const spinner = document.querySelector('.spinner-product');
		spinner.classList.add('disabled');

		fetch(`/products-json/?page=${prevPageNumber}`, {
			method: 'GET',
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then((response) => {
				this.currentPagination = prevPageNumber;

				let currentUrl = window.location.href;
				let newUrl = new URL(currentUrl);
				newUrl.searchParams.set('page', this.currentPagination);
				window.history.replaceState({}, '', newUrl);

				this.items = response.products.map((itemData) => {
					return this.createItem(itemData);
				});

				this.render();

				if (this.currentPagination === 1) {
					document.querySelector('.pagination__button_prev').disabled = true;
					localStorage.setItem('prevButtonDisabled', 'true');
				}

				if (response.nextPage.length !== 0) {
					document.querySelector('.pagination__button_next').disabled = false;
					localStorage.setItem('nextButtonDisabled', 'false');
				}

				const spinner = document.querySelector('.spinner-product');
				spinner.classList.remove('disabled');
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
				const spinner = document.querySelector('.spinner-product');
				spinner.classList.remove('disabled');
			});
	}

	handleNextPaginationButtonClick() {
		this.currentPagination = new URLSearchParams(window.location.search).get('page');

		let nextPageNumber = parseInt(this.currentPagination) + 1;

		this.currentPagination = nextPageNumber;

		let currentUrl = window.location.href;
		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', this.currentPagination);
		window.history.replaceState({}, '', newUrl);

		const spinner = document.querySelector('.spinner-product');
		spinner.classList.add('disabled');

		fetch(`/products-json/?page=${nextPageNumber}`, {
			method: 'GET',
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then((response) => {
				this.items = response.products.map((itemData) => {
					return this.createItem(itemData);
				});

				this.render();

				if (response.nextPage.length === 0) {
					document.querySelector('.pagination__button_next').disabled = true;
					localStorage.setItem('nextButtonDisabled', 'true');
				}

				if (this.currentPagination > 1)
				{
					document.querySelector('.pagination__button_prev').disabled = false;
					localStorage.setItem('prevButtonDisabled', 'false');
				}

				const spinner = document.querySelector('.spinner-product');
				spinner.classList.remove('disabled');
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
				const spinner = document.querySelector('.spinner-product');
				spinner.classList.remove('disabled');
			});
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

			this.items = response.products.map((itemData) => {
				return this.createItem(itemData)
			})

			this.render();

			if (response.nextPage.length === 0) {
				document.querySelector('.pagination__button_next').disabled = true;
				localStorage.setItem('nextButtonDisabled', 'true');
			} else {
				document.querySelector('.pagination__button_next').disabled = false;
				localStorage.setItem('nextButtonDisabled', 'false');
			}

			if ((this.currentPagination - 1) === 1) {
				document.querySelector('.pagination__button_prev').disabled = true;
				localStorage.setItem('prevButtonDisabled', 'true');
			} else {
				document.querySelector('.pagination__button_prev').disabled = false;
				localStorage.setItem('prevButtonDisabled', 'false');
			}

			spinner.classList.remove('disabled');
		})
		.catch((error) => {
			console.error('Error while changing item:', error);
			spinner.classList.remove('disabled');
		});
	}

	handleSearchButtonSubmit()
	{
		const inputSearch = document.getElementById('search');
		const title = inputSearch.value;

		let currentUrl = window.location.href;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('title', title);

		window.history.replaceState({}, '', newUrl);

		const spinner = document.querySelector('.spinner-product');
		spinner.classList.add('disabled');

		fetch(
			`/search-json/?title=${title}`,
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
			.then((response) => {
				/*if (response.status === 204) {
					this.items = [];
				}
				if (response.status === 200) {*/
					this.items = response.products.map((itemData) => {
						return this.createItem(itemData)
					})
				/*}
*/
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

	render()
	{
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
		})

		buttonRemove.forEach(btn => {
			btn.addEventListener('click', this.handleBasketRemoveButtonSubmit.bind(this))
		})

		this.renderPagination();
		this.renderSearchForm();
	}

	renderPagination()
	{
		const paginationContainer = document.createElement('div');
		paginationContainer.id = 'buttonPagination'
		paginationContainer.classList.add('pagination');

		const prevButton = document.createElement('button');
		prevButton.classList.add('pagination__button-switch', 'pagination__button_prev');
		prevButton.innerText = 'Назад';
		prevButton.addEventListener('click', this.handlePrevPaginationButtonClick.bind(this));

		if (!localStorage.getItem('prevButtonDisabled')) {
			prevButton.disabled = true;
		}

		const prevButtonDisabled = localStorage.getItem('prevButtonDisabled');
		if (prevButtonDisabled === 'true') {
			prevButton.disabled = true;
		}

		paginationContainer.append(prevButton);

		let current = 0;

		if (!this.currentPagination) {
			this.currentPagination = 1;
		}

		if (parseInt(this.currentPagination) === 1) {
			current = 1;
		}

		for (let i = 1; i <= parseInt(this.currentPagination) + current; i++) {
			const buttonPagination = document.createElement('button');
			buttonPagination.classList.add('pagination__button');
			buttonPagination.innerText = String(i);
			buttonPagination.addEventListener('click', this.handleChangePaginationButtonClick.bind(this));

			const currentPage = new URLSearchParams(window.location.search).get('page');
			if (buttonPagination.innerText === currentPage) {
				buttonPagination.classList.add('is-active');
			}

			paginationContainer.append(buttonPagination)

			this.rootNode.append(paginationContainer);
		}

		const nextButton = document.createElement('button');
		nextButton.classList.add('pagination__button-switch', 'pagination__button_next');
		nextButton.innerText = 'Вперед';
		nextButton.addEventListener('click', this.handleNextPaginationButtonClick.bind(this));

		const nextButtonDisabled = localStorage.getItem('nextButtonDisabled');
		if (nextButtonDisabled === 'true') {
			nextButton.disabled = true;
		}
		paginationContainer.append(nextButton);
	}

	renderSearchForm()
	{
		const containerForm = document.querySelector('.header__form');

		containerForm.innerHTML = '';

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

		containerForm.append(form);

		return containerForm;
	}
}
