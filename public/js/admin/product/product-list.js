import { ProductItem } from "./product-item.js";

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
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

		this.createItemsContainer()
	}

	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		return new ProductItem(itemData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('product-list');

		this.rootNode.append(this.itemsContainer);
	}

	handleEditButtonClick(item)
	{
		console.log(1);
	}
	handleRemoveButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);
		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to delete this product: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const removeParams = {
				id: item.id,
			}

			fetch(
				'/product/remove/',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(removeParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result === 'Y')
					{
						this.items.splice(itemIndex, 1);
						this.render();
					}
					else
					{
						console.error('Error while deleting item.');
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
				})
		}
	}
	render()
	{
		this.itemsContainer.innerHTML = '';

		const row = document.createElement('div');
		row.classList.add('row', 'product__row');

		const titleColumn = document.createElement('div');
		titleColumn.classList.add('col-3');
		titleColumn.innerText = 'Title';

		const descriptionColumn = document.createElement('div');
		descriptionColumn.classList.add('col-3');
		descriptionColumn.innerText = 'Description';

		const priceColumn = document.createElement('div');
		priceColumn.classList.add('col-3');
		priceColumn.innerText = 'Price';

		const actionsColumn = document.createElement('div');
		actionsColumn.classList.add('col-3');
		actionsColumn.innerText = 'Actions';

		row.append(titleColumn, descriptionColumn, priceColumn, actionsColumn)
		this.itemsContainer.append(row);

		this.items.forEach((item) => {
			this.itemsContainer.append(item.render());
		})
	}
}
