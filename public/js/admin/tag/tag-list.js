import { TagItem } from "./tag-item.js";
import {Error} from "../../main/error/error.js";

export class TagList
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
		return new TagItem(itemData);
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
		const id = document.getElementById('tagId');
		const title = document.getElementById('tagTitle');

		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

		id.innerText = item['id'];
		title.value = item['title'];

		const buttonAdd = document.getElementById('add');
		const buttonEdit = document.getElementById('changed');

		if (buttonAdd) {
			buttonAdd.style.display = 'none';
		}

		buttonEdit.style.display = 'block';

		formEdit.style.display = 'block';
	}

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleAcceptEditButtonClick()
	{
		const shouldChange = confirm(`Are you sure you want to change this tag: ?`)
		if (!shouldChange)
		{
			return;
		}

		const id = document.getElementById('tagId').innerText;
		const title = document.getElementById('tagTitle').value;

		const formContainer = document.querySelector('.form');
		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

		const changeParams = {
			id: Number(id),
			title: title,
		}

		const buttonEdit = document.getElementById(changeParams.id + 'edit');
		buttonEdit.disabled = true;

		fetch(
			'/admin/tag/change/',
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
							item.title = changeParams.title;
						}
					})

					buttonEdit.disabled = false;

					await this.render();
				} else {
					console.error(response.errors);
					buttonEdit.disabled = false;
					new Error(`Не удалось изменить тег`, formContainer).printError();
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				new Error(`Что-то пошло не так`, formContainer).printError();
				buttonEdit.disabled = false;
			})
	}
	handleRemoveButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);

		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to delete this tag: ${item.title}?`)
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
				'/admin/tag/',
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
				.then((response) => {
					if (response.result === true)
					{
						this.items.splice(itemIndex, 1);
						buttonRemove.disabled = false;
						this.render();
					}
					else
					{
						console.error('Error while disabling item.');
						buttonRemove.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while disabling item.');
					buttonRemove.disabled = false;
				})
		}
	}

	handleAcceptAddButtonClick() {
		const shouldRemove = confirm(`Are you sure you want to delete this product: ?`)
		if (!shouldRemove)
		{
			return;
		}

		const title = document.getElementById('tagTitle').value;

		const addParams = {
			title: title,
		}

		const buttonAdd = document.getElementById('add');
		buttonAdd.disabled = true;

		const formContainer = document.querySelector('.form');
		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

		fetch(
			'/admin/tag/add/',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(addParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.id)
				{
					const table = document.querySelector('.table');
					let isExecuted = false;

					this.items.forEach(item => {
						if (!isExecuted) {
							item.id = response.id;
							item.title = addParams.title;
							table.append(item.render());

							isExecuted = true;
						}
					});

					const formEdit = document.querySelector('.form__box');
					formEdit.style.display = 'none';

					buttonAdd.disabled = false;
				}
				else
				{
					console.error(response.errors);
					new Error(`Не удалось добавить тег`, formContainer).printError();
					buttonAdd.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				new Error(`Что-то пошло не так`, formContainer).printError();
				buttonAdd.disabled = false;
			})
	}

	handleChangePaginationButtonClick()
	{
		const page = event.target.innerText;

		let currentUrl = window.location.href;

		this.currentPagination = page;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		fetch(
			`/tagsAdmin-json/?page=${page}`,
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

				this.items = response.tags.map((itemData) => {
					return this.createItem(itemData)
				})

				await this.render();
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	handleAddButtonClick() {
		const inputTitle = document.getElementById('tagTitle');
		const buttonAdd = document.getElementById('add');
		const buttonEdit = document.getElementById('changed');

		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

		inputTitle.value = '';

		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'block';

		buttonAdd.style.display = 'block';
		buttonEdit.style.display = 'none';
	}

	async render() {
		if (this.items.length === 0) {
			this.rootNode.innerHTML = '';
			const modal = new Error('Данная страница не найдена!', null, '/admin/?entity=tags').render();
			this.rootNode.append(modal);

			return false;
		}
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
		columnAction.innerText = 'Действие';

		containerColumn.append(columnAction);
		table.append(containerColumn);

		const addButton = document.createElement('button');
		addButton.classList.add('form__button', 'form__button_add');
		addButton.id = 'addOpen';
		addButton.innerText = 'Добавить';
		addButton.addEventListener('click', this.handleAddButtonClick.bind(this));

		this.itemsContainer.append(addButton, table, this.renderForm());

		this.items.forEach((item) => {
			table.append(item.render());
		})

		await this.renderPagination()
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
		spanId.id = 'tagId';
		spanId.style.display = 'none';

		const titleLabel = document.createElement('label');
		titleLabel.classList.add('form__label');
		titleLabel.htmlFor = 'tagTitle';
		titleLabel.innerText = 'Название';

		const titleInput = document.createElement('input');
		titleInput.classList.add('form__input');
		titleInput.id = 'tagTitle';
		titleInput.type = 'text';
		titleInput.name = 'tagTitle';

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button','form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this));

		const acceptAddButton = document.createElement('button');
		acceptAddButton.classList.add('form__button', 'form__button_add');
		acceptAddButton.id = 'add';
		acceptAddButton.type = 'submit';
		acceptAddButton.name = 'addProduct';
		acceptAddButton.innerText = 'Добавить';
		acceptAddButton.addEventListener('click', this.handleAcceptAddButtonClick.bind(this));

		form.append(spanId, titleLabel, titleInput, acceptButton, acceptAddButton);
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
			`/tagsAdmin-json/?page=1`,
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
