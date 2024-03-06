import { OrderItem } from "./order-item.js";
import {Error} from "../../main/error/error.js";

export class OrderList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	currentPagination = new URLSearchParams(window.location.search).get('page');
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
		return new OrderItem(itemData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('product-list');

		this.rootNode.append(this.itemsContainer);
	}

	handleEditButtonClick(item)
	{
		const formEdit = document.querySelector('.form__box');
		const id = document.getElementById('orderId');
		const deliveryAddress = document.getElementById('deliveryAddress');
		const name = document.getElementById('name');
		const surname = document.getElementById('surname');

		id.innerText = item['id'];
		deliveryAddress.value = item['deliveryAddress'];
		name.value = item['name'];
		surname.value = item['surname'];
		/*products.append(productsDropDown);*/
		formEdit.style.display = 'block';
	}

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleAcceptEditButtonClick()
	{
		const shouldRemove = confirm(`Are you sure you want to change this product: ?`)
		if (!shouldRemove)
		{
			return;
		}

		const id = document.getElementById('orderId').innerText;
		const deliveryAddress = document.getElementById('deliveryAddress').value;
		const name = document.getElementById('name').value;
		const surname = document.getElementById('surname').value;


		const changeParams = {
			id: Number(id),
			deliveryAddress: deliveryAddress,
			name: name,
			surname: surname,
		}

		const buttonEdit = document.getElementById(changeParams.id + 'edit');
		buttonEdit.disabled = true;

		fetch(
			'/admin/order/change/',
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
			.then(async (response) => {
				if (response.result === true) {
					this.items.forEach(item => {
						if (item.id === changeParams.id) {
							item.deliveryAddress = changeParams.deliveryAddress;
							item.name = changeParams.name;
							item.surname = changeParams.surname;
							return true;
						}
					})

					buttonEdit.disabled = false;

					await this.render();
				} else {
					console.error(response.errors);
					buttonEdit.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				buttonEdit.disabled = false;
			})
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
				'/admin/order/',
				{
					method: 'DELETE',
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
					if (response.result === true) {
						this.items.splice(itemIndex, 1);
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

	handleChangePaginationButtonClick() {
		const page = event.target.innerText;

		let currentUrl = window.location.href;

		this.currentPagination = page;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		fetch(
			`/orderAdmin-json/?page=${page}`,
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

				response.orders.forEach(order => {

					let arr = [];

					for (const key in order.products) {
						const info = order.products[key].info;
						arr.push(info)
					}

					order.products = arr;
				})

				console.log(response.orders);

				this.items = response.orders.map((itemData) => {
					return this.createItem(itemData)
				})

				await this.render();
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	async render() {
		if (this.items.length === 0) {
			this.rootNode.innerHTML = '';
			const modal = new Error('Данная страница не найдена!', null, '/admin/?entity=orders').render();
			this.rootNode.append(modal);

			return false;
		}
		this.itemsContainer.innerHTML = '';

		const table = document.createElement('table');
		table.classList.add('table');

		const containerColumn = document.createElement('tr');
		containerColumn.classList.add('table__tr');

		this.columns.forEach(column => {
			if (column === 'user_id')
				return;
			const tableColumn = document.createElement('th');
			tableColumn.classList.add('table__th', 'table__th-heading');
			tableColumn.innerText = column;

			containerColumn.append(tableColumn);
		})

		const productsColumn = document.createElement('th');
		productsColumn.classList.add('table__th', 'table__th-heading');
		productsColumn.innerText = 'Продукты';
		containerColumn.append(productsColumn);

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'действие';

		containerColumn.append(columnAction);
		table.append(containerColumn);


		this.itemsContainer.append(table, this.renderForm());

		this.items.forEach((item) => {
			table.append(item.render());
		})

		await this.renderPagination();
	}

	renderForm()
	{
		const formBox = document.createElement('div');
		formBox.classList.add('form__box');

		const formContainer = document.createElement('div');
		formContainer.classList.add('form__container');

		const closeButton = document.createElement('button');
		closeButton.classList.add('form__close');
		closeButton.addEventListener('click', this.handleEditCloseButtonClick);
		const closeIcon = document.createElement('i');
		closeIcon.classList.add('form__close-icon', 'material-icons');
		closeIcon.innerText = 'close';
		closeButton.append(closeIcon);

		const form = document.createElement('div');
		form.classList.add('form');

		const spanId = document.createElement('span');
		spanId.id = 'orderId';
		spanId.style.display = 'none';

		const deliveryAddressLabel = document.createElement('label');
		deliveryAddressLabel.classList.add('form__label');
		deliveryAddressLabel.htmlFor = 'deliveryAddress';
		deliveryAddressLabel.innerText = 'Адрес доставки';

		const deliveryAddressInput = document.createElement('input');
		deliveryAddressInput.classList.add('form__input');
		deliveryAddressInput.id = 'deliveryAddress';
		deliveryAddressInput.type = 'text';
		deliveryAddressInput.name = 'deliveryAddress';

		const nameLabel = document.createElement('label');
		nameLabel.classList.add('form__label');
		nameLabel.htmlFor = 'name';
		nameLabel.innerText = 'Имя';

		const nameInput = document.createElement('input');
		nameInput.classList.add('form__input');
		nameInput.id = 'name';
		nameInput.type = 'text';
		nameInput.name = 'name';

		const surnameLabel = document.createElement('label');
		surnameLabel.classList.add('form__label');
		surnameLabel.htmlFor = 'name';
		surnameLabel.innerText = 'Фамилия';

		const surnameInput = document.createElement('input');
		surnameInput.classList.add('form__input');
		surnameInput.id = 'surname';
		surnameInput.type = 'text';
		surnameInput.name = 'surname';

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button','form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this))

		form.append(spanId, deliveryAddressLabel, deliveryAddressInput,
			nameLabel, nameInput,
			surnameLabel, surnameInput,/* productsLabel, dropDownContent*/ acceptButton);
		formContainer.append(closeButton, form);
		formBox.append(formContainer);

		return formBox;
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
			`/orderAdmin-json/?page=1`,
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
