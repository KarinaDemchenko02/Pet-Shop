import {TagItem} from "./tag-item.js";
import {ProductItem} from "../product/product-item.js";
import {ProductList} from "../product/product-list.js";
import {BasketItem} from "../basket/basket-item.js";

export class TagList
{
	attachToNodeId = '';
	rootNode;
	items = [];
	basketItem = [];

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
	handleFilterTagButtonClick(item)
	{
		if (this.items)
		{
			const tag = item.id;

			let currentUrl = window.location.href;

			// let page;
			//
			// if (new URLSearchParams(window.location.search).get('page')) {
			// 	page = new URLSearchParams(window.location.search).get('page');
			// }

			let newUrl = new URL(currentUrl);
			newUrl.searchParams.set('tag', tag);

			window.history.replaceState({}, '', newUrl);

			const spinner = document.querySelector('.spinner-product');
			spinner.classList.add('disabled');


			fetch(
				`/tags-json/?tag=${item.id}`,
				{
					method: 'GET',
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					let products = response.products.map((itemData) => {
						return this.createProduct(itemData);
					});

					const productsList = new ProductList({
						attachToNodeId: 'product__list-container',
						items: products,
						basketItem: this.basketItem
					});

					productsList.render();

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

		this.items.forEach((item) => {
			this.rootNode.append(item.render())
		});
	}
}