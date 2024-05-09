import {TagItem} from "./tag-item.js";
import {ProductItem} from "../product/product-item.js";
import {ProductList} from "../product/product-list.js";
import {BasketItem} from "../basket/basket-item.js";
import {Search} from "../product/search/search.js";

export class TagList
{
	attachToNodeId = '';
	rootNode;
	items = [];
	basketItem = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');

	constructor({ attachToNodeId = '', items, basketItem})
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

		this.allProducts = items.map((itemData) => {
			return this.createProduct(itemData)
		})
	}

	createItem(itemData)
	{
		itemData.filterTagButtonHandler = this.handleFilterTagButtonClick.bind(this);
		return new TagItem(itemData);
	}

	createProduct(itemData)
	{
		return new ProductItem(itemData);
	}

	createBasket(itemData)
	{
		return new BasketItem(itemData)
	}

	handlePaginationButtonClick(event)
	{
		if (this.items)
		{
			const page = event.target.innerText;
			const tag = new URLSearchParams(window.location.search).get('tag');

			let currentUrl = window.location.href;

			let newUrl = new URL(currentUrl);
			newUrl.searchParams.set('tag', tag);
			newUrl.searchParams.set('page', page);

			window.history.replaceState({}, '', newUrl);

			const urlParams = new URLSearchParams(window.location.search);
			const paramTitle = urlParams.get('title');

			fetch(
				`/tags-json/?tag=${tag}&page=${page}&title=${paramTitle}`,
				{
					method: 'GET',
				}
			)
			.then((response) => {
				return response.json();
			})
			.then(async (response) => {
				let products;

				if (paramTitle !== null) {
					if (response.productsByTagTitleNext.length !== 0) {
						this.currentPagination = Number(page) + 1;
					}

					products = response.productsByTagTitle.map((itemData) => {
						return this.createProduct(itemData)
					})
				} else {
					if (response.nextPage.length !== 0) {
						this.currentPagination = Number(page) + 1;
					}

					products = response.products.map((itemData) => {
						return this.createProduct(itemData);
					});
				}

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

	handleFilterTagButtonClick(item)
	{
		if (this.items)
		{
			const tag = item.id;

			let page = '1';

			this.currentPagination = page;

			let currentUrl = window.location.href;

			let newUrl = new URL(currentUrl);

			let currentTags = newUrl.searchParams.getAll('tag');

			const checkboxes = document.querySelectorAll('.tags__checkbox');

			currentTags.push(tag.toString());

			//currentTags = currentTags.filter((item, index) => currentTags.indexOf(item) === index);

			if (currentTags[1]) {
				if (currentTags[0].includes(currentTags[1])) {
					const newArr = currentTags[0].split(',');
					const index = newArr.indexOf(currentTags[1]);
					newArr.splice(index, 1);
					currentTags[0] = newArr.join(',');

					currentTags.splice(1, 1);
				}
			}

			if (currentTags[0].length === 0) {
				newUrl.searchParams.set('tag', '');
			} else {
				newUrl.searchParams.set('tag', currentTags.join(','));
			}
			checkboxes.forEach(checkbox => {
				if (checkbox.checked) {
					newUrl.searchParams.set('tag', currentTags.join(','));
				} else if (currentTags[0].length === 1) {
					const checkboxWithId = document.getElementById(`tag:${currentTags[0]}`);
					if (!checkboxWithId.checked) {
						newUrl.searchParams.delete('tag');
						return false;
					}
				}
			})

			newUrl.searchParams.set('page', page);

			window.history.replaceState({}, '', newUrl);

			const inputCheckbox = document.querySelectorAll('.tags__checkbox');
			inputCheckbox.forEach(checkbox => {
				checkbox.disabled = true;
			})

			const spinner = document.querySelector('.spinner-product');
			if (spinner) {
				spinner.classList.add('disabled');
			}

			const urlParams = new URLSearchParams(window.location.search);
			const paramTitle = urlParams.get('title');
			const paramTag = urlParams.get('tag');

			fetch(
				`/tags-json/?tag=${currentTags.join(',')}&page=${page}&title=${paramTitle}`,
				{
					method: 'GET',
				}
			)
				.then((response) => {
					return response.json();
				})
				.then(async (response) => {
					let products;

					if (paramTag === null) {
						if (paramTitle !== null && paramTitle !== '') {
							products = response.productTitle.map((itemData) => {
								return this.createProduct(itemData)
							})

							const productsList = new ProductList({
								attachToNodeId: 'product__list-container',
								items: products,
								basketItem: this.basketItem
							});

							const searchList = new Search({
								attachToNodeId: 'header-search',
								items: products,
								basketItem: this.basketItem
							})

							await productsList.render();

							searchList.renderPagination();

						} else {
							products = response.allProducts.map((itemData) => {
								return this.createProduct(itemData);
							});

							const productsList = new ProductList({
								attachToNodeId: 'product__list-container',
								items: products,
								basketItem: this.basketItem
							});

							await productsList.render();

							document.getElementById('buttonPagination').innerHTML = '';

							await productsList.renderPagination();
						}

					} else {
						if (paramTitle !== null) {
							if (response.productsByTagTitleNext.length !== 0) {
								this.currentPagination = Number(page) + 1;
							}

							products = response.productsByTagTitle.map((itemData) => {
								return this.createProduct(itemData)
							})

						} else {
							if (response.nextPage.length !== 0) {
								this.currentPagination = Number(page) + 1;
							}

							products = response.products.map((itemData) => {
								return this.createProduct(itemData);
							});
						}

						const productsList = new ProductList({
							attachToNodeId: 'product__list-container',
							items: products,
							basketItem: this.basketItem
						});

						const result = await productsList.render();
						if (result === false) {
							if (spinner) {
								spinner.classList.remove('disabled');
							}

							inputCheckbox.forEach(checkbox => {
								checkbox.disabled = false;
							});

							return false;
						}

						this.renderPagination();
					}

					if (currentTags[0].length === 0) {
						console.log(response.allProducts);
					}

					let products = response.products.map((itemData) => {
						return this.createProduct(itemData);
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
					if (spinner) {
						spinner.classList.remove('disabled');
					}

					inputCheckbox.forEach(checkbox => {
						checkbox.disabled = false;
					})
				})
				.catch((error) => {
					console.error('Error while deleting item.', error);
					const spinner = document.querySelector('.spinner-product');
					spinner.classList.remove('disabled');

					inputCheckbox.forEach(checkbox => {
						checkbox.disabled = false;
					})
				})
		}
	}

	render()
	{
		if (this.items.length === 0) {
			return false;
		}

		this.rootNode.innerHTML = '';

		this.items.forEach((item) => {
			this.rootNode.append(item.render())
		});
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