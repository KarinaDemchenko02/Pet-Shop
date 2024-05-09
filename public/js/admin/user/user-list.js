import {UserItem} from "./user-item.js";
import {Error} from "../../main/error/error.js";

export class UserList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	orders = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');

	constructor({ attachToNodeId = '', items, columns, orders })
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

		this.orders = orders;

		this.createItemsContainer()
	}

	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
		return new UserItem(itemData);
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
				'/admin/user/restore/',
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
				.then(async (response) => {
					if (response.result) {
						this.items[itemIndex].isActive = true;
						buttonRestore.disabled = false;
						await this.render();
					} else {
						console.error('Error while deleting item.');
						buttonRestore.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRestore.disabled = false;
				})
		}
	}

	handleRemoveButtonClick(item) {
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
				'/admin/user/disable/',
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
				.then(async (response) => {
					if (response.result) {
						this.items[itemIndex].isActive = false;
						buttonRemove.disabled = false;
						await this.render();
					} else {
						console.error('Error while deleting item.');
						buttonRemove.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRemove.disabled = false;
				})
		}
	}

	handleChangePaginationButtonClick(event)
	{
		const page = event.target.innerText;

		let currentUrl = window.location.href;

		this.currentPagination = page;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		fetch(
			`/userAdmin-json/?page=${page}`,
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
			.then(async (response) => {
				if (response.nextPage.length !== 0) {
					this.currentPagination = Number(page) + 1;
				}

				this.items = response.users.map((itemData) => {
					return this.createItem(itemData)
				})

				await this.render();
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('product-list');

		this.rootNode.append(this.itemsContainer);
	}

	async render() {
		if (this.items.length === 0) {
			this.rootNode.innerHTML = '';
			const modal = new Error('Данная страница не найдена!', null, '/admin/?entity=users').render();
			this.rootNode.append(modal);

			return false;
		}

		this.itemsContainer.innerHTML = '';

		const table = document.createElement('table');
		table.classList.add('table');

		const containerColumn = document.createElement('tr');
		containerColumn.classList.add('table__tr');

		const columnId = document.createElement('th');
		columnId.classList.add('table__th', 'table__th-heading');
		columnId.innerText = 'ID';

		const columnName = document.createElement('th');
		columnName.classList.add('table__th', 'table__th-heading');
		columnName.innerText = 'Имя';

		const columnSurname = document.createElement('th');
		columnSurname.classList.add('table__th', 'table__th-heading');
		columnSurname.innerText = 'Фамилия';

		const columnEmail = document.createElement('th');
		columnEmail.classList.add('table__th', 'table__th-heading');
		columnEmail.innerText = 'Email';

		const columnRole = document.createElement('th');
		columnRole.classList.add('table__th', 'table__th-heading');
		columnRole.innerText = 'Роль';

		const columnOrders = document.createElement('th');
		columnOrders.classList.add('table__th', 'table__th-heading');
		columnOrders.innerText = 'Заказы';

		const columnPhone = document.createElement('th');
		columnPhone.classList.add('table__th', 'table__th-heading');
		columnPhone.innerText = 'Телефон';

		const columnActive = document.createElement('th');
		columnActive.classList.add('table__th', 'table__th-heading');
		columnActive.innerText = 'isActive';

		containerColumn.append(columnId, columnName, columnSurname, columnEmail, columnPhone, columnRole, columnOrders, columnActive);

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'действие';


		containerColumn.append(columnAction);
		table.append(containerColumn);

		this.itemsContainer.append(table);

		this.items.forEach((item) => {
			table.append(item.render());
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

			this.itemsContainer.append(paginationContainer);
		}
	}

	checkPageNumberOne()
	{
		return fetch(
			`/userAdmin-json/?page=1`,
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
