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
		})
	}
}