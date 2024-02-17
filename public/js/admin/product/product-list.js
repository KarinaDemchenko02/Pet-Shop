import { ProductItem } from "./product-item.js";

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	constructor({ attachToNodeId = '', items, columns })
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

		this.columns = columns;

		this.createItemsContainer()
	}


	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
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
				'/admin/remove/',
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
						this.items[itemIndex].isActive = false;
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
				.finally()
				{
					const buttonRemove = document.getElementById(item.id + 'remove');
					buttonRemove.disabled = true;
				}

		}
	}

	handleRestoreButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);
		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to restore this product: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const removeParams = {
				id: item.id,
			}

			fetch(
				'/admin/restore/',
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
						this.items[itemIndex].isActive = true;
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
				.finally()
				{
					const buttonRestore = document.getElementById(item.id + 'restore');
					buttonRestore.disabled = true;
				}
		}
	}
	render()
	{
		this.itemsContainer.innerHTML = '';

		const table = document.createElement('table');
		table.classList.add('table');

		const containerColumn = document.createElement('tr');
		containerColumn.classList.add('table__tr');

		this.columns.forEach(column => {
			const tableColumn = document.createElement('th');
			tableColumn.classList.add('table__th', 'table__th-heading');
			tableColumn.innerText = column;

			containerColumn.append(tableColumn);
		})

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'действие';

		containerColumn.append(columnAction);
		table.append(containerColumn);

		this.itemsContainer.append(table);

		this.items.forEach((item) => {
			table.append(item.render());
		})
	}
}
