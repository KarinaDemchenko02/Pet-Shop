export class UserItem
{
	id;
	name;
	surname;
	email;
	roleTitle;
	phoneNumber;
	orders;
	isActive;
	removeButtonHandler;
	restoreButtonHandler;

	constructor({ id, name, surname, email, roleTitle, phoneNumber, orders, isActive, removeButtonHandler, restoreButtonHandler })
	{
		this.id = Number(id);
		this.name = String(name);
		this.surname = String(surname);
		this.email = String(email);
		this.roleTitle = String(roleTitle);
		this.phoneNumber = String(phoneNumber);
		this.orders = orders;
		this.isActive = Boolean(isActive);

		if (typeof removeButtonHandler === 'function')
		{
			this.removeButtonHandler = removeButtonHandler;
		}

		if (typeof restoreButtonHandler === 'function')
		{
			this.restoreButtonHandler = restoreButtonHandler;
		}
	}

	render()
	{
		const trProduct = document.createElement('tr');
		trProduct.id = String(this.id) + 'tr';
		trProduct.classList.add('table__tr');

		const idColumn = document.createElement('td');
		idColumn.classList.add('table__th', 'table__th_id');
		idColumn.innerText = this.id;

		const nameColumn = document.createElement('td');
		nameColumn.classList.add('table__th', 'table__th_name');
		nameColumn.innerText = this.name;

		const surnameColumn = document.createElement('td');
		surnameColumn.classList.add('table__th', 'table__th_name');
		surnameColumn.innerText = this.surname;

		const emailColumn = document.createElement('td');
		emailColumn.classList.add('table__th', 'table__th_title');
		emailColumn.innerText = this.email;

		const roleColumn = document.createElement('td');
		roleColumn.classList.add('table__th', 'table__th_role');
		roleColumn.innerText = this.roleTitle;

		const phoneColumn = document.createElement('td');
		phoneColumn.classList.add('table__th', 'table__th_price');
		phoneColumn.innerText = this.phoneNumber;

		const ordersColumn = this.createOrderColumn();
		ordersColumn.classList.add('table__th', 'table__th_tags');

		const activeColumn = document.createElement('td');
		activeColumn.classList.add('table__th', 'table__th_active');
		activeColumn.innerText = this.isActive;

		const spinnerRemove = document.createElement('div');
		spinnerRemove.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRemove = document.createElement('span');
		spinnerLoadingRemove.innerText = 'Loading...';
		spinnerLoadingRemove.classList.add('visually-hidden');
		spinnerRemove.append(spinnerLoadingRemove);

		const removeButton = document.createElement('button');
		removeButton.classList.add('table__button', 'table__button_delete');
		removeButton.id = String(this.id) + 'remove';
		removeButton.innerText = 'Удалить';
		removeButton.addEventListener('click', this.handleRemoveButtonClick.bind(this));
		removeButton.append(spinnerRemove);

		const spinnerRestore = document.createElement('div');
		spinnerRestore.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRestore = document.createElement('span');
		spinnerLoadingRestore.innerText = 'Loading...';
		spinnerLoadingRestore.classList.add('visually-hidden');
		spinnerRestore.append(spinnerLoadingRestore);

		const restoreButton = document.createElement('button');
		restoreButton.classList.add('table__button', 'table__button_restore');
		restoreButton.id = String(this.id) + 'restore';
		restoreButton.innerText = 'Восстановить';
		restoreButton.append(spinnerRestore);
		restoreButton.addEventListener('click', this.handleRestoreButtonClick.bind(this));

		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(removeButton, restoreButton);

		trProduct.append(idColumn, nameColumn, surnameColumn, emailColumn, phoneColumn, roleColumn, ordersColumn, activeColumn, actionsColumn);
		return trProduct;
	}

	handleRemoveButtonClick()
	{
		if (this.removeButtonHandler)
		{
			this.removeButtonHandler(this);
		}
	}

	handleRestoreButtonClick()
	{
		if (this.restoreButtonHandler)
		{
			this.restoreButtonHandler(this);
		}
	}

	renderDate(timestamp = null)
	{
		let date;

		if (timestamp === null)
		{
			date = new Date();
		}
		else
		{
			date = new Date(timestamp * 1000);
		}

		const year = date.getFullYear();
		const month = ("0" + (date.getMonth() + 1)).slice(-2);
		const day = date.getDate();
		const hours =  ("0" + date.getHours()).slice(-2);
		const minutes = ("0" + date.getMinutes()).slice(-2);
		const seconds = ("0" + date.getSeconds()).slice(-2);

		return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
	}

	createOrderColumn()
	{
		const orderColumn = document.createElement('td');
		orderColumn.classList.add('dropdown_list');

		const dropdownButton = document.createElement('dropdown_button');
		dropdownButton.innerText = 'Показать заказы';
		dropdownButton.id = String(this.id) + 'dropdown';
		dropdownButton.addEventListener('click', this.showDropDownTags.bind(this));

		const orderRowContainer = document.createElement('div');
		orderRowContainer.id = String(this.id) + 'productsRowContainer';
		orderRowContainer.classList.add('productsRowContainer');

		const labelsContainer = document.createElement('ul');
		labelsContainer.id = 'productLabel';
		labelsContainer.classList.add('productLabel');
		const idOrderLabel = document.createElement('label');
		idOrderLabel.innerText = 'Id заказа';
		const statusOrderLabel = document.createElement('label');
		statusOrderLabel.innerText = 'Статус заказа';

		labelsContainer.append(idOrderLabel, statusOrderLabel);

		orderRowContainer.append(labelsContainer);
		orderColumn.append(dropdownButton, orderRowContainer);
		return orderColumn;
	}

	showDropDownTags()
	{
		let tags = document.getElementById(String(this.id) + 'productsRowContainer');

		if (tags.style.display === "block") {
			tags.style.display = "none";
		} else {
			tags.style.display = "block";
		}
	}
}
