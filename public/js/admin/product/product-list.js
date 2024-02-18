import { ProductItem } from "./product-item.js";
import {ChangeForm} from "./change-form.js";

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	form;
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

		this.createFormBox();
	}


	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.openEditButtonHandler = this.handleOpenEditButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
		return new ProductItem(itemData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('product-list');

		this.rootNode.append(this.itemsContainer);
	}

	handleOpenEditButtonClick(item)
	{
		this.form.formBox.style.display = 'block';
	}

	handleAcceptEditButtonClick(item)
	{

		const itemIndex = this.items.indexOf(item);
		console.log(item);
		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to delete this product: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const title = document.getElementById('title').value;
			const desc = document.getElementById('desc').value;
			const price = document.getElementById('price').value;
			const tags = document.getElementById('tags').value;

			const changeParams = {
				id: item.id,
				title: title,
				description: desc,
				price: price,
				tags: tags,
			}

			fetch(
				'/admin/product/change/',
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(changeParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result === true)
					{
						this.items[itemIndex].title = title;
						this.items[itemIndex].description = desc;
						this.items[itemIndex].price = price;
						this.items[itemIndex].tags = tags;
						this.render();
					}
					else
					{
						console.error(response.errors);
					}
				})
				.catch((error) => {
					console.error('Error while changing item.');
				})
				.finally()
			{
				const buttonRemove = document.getElementById(item.id + 'remove');
				buttonRemove.disabled = true;
			}

		}
	}

	handleCloseEditButtonClick(item)
	{
		this.form.formBox.style.display = 'none';
	}

	createFormBox()
	{
		this.form = new ChangeForm(
			this.handleAcceptEditButtonClick.bind(this),
			this.handleCloseEditButtonClick.bind(this)
		);
		this.rootNode.append(this.form.render());
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

			const buttonRemove = document.getElementById(item.id + 'remove');
			buttonRemove.disabled = true;

			fetch(
				'/admin/product/disable/',
				{
					method: 'PATCH',
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
					if (response.result === true)
					{
						this.items[itemIndex].isActive = false;
						buttonRemove.disabled = false;
						this.render();
					}
					else
					{
						console.error(response.errors);
						buttonRemove.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRemove.disabled = false;
				})
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

			const buttonRestore = document.getElementById(item.id + 'restore');
			buttonRestore.disabled = true;

			fetch(
				'/admin/product/restore/',
				{
					method: 'PATCH',
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
					if (response.result === true)
					{
						this.items[itemIndex].isActive = true;
						buttonRestore.disabled = false;
						this.render();
					}
					else
					{
						console.error(response.errors);
						buttonRestore.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRestore.disabled = false;
				})
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
