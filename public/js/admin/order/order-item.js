export class OrderItem
{
	id;
	products;
	user_id;
	deliveryAddress;
	createdAt;
	editedAt;
	name;
	surname;
	status;
	editButtonHandler;
	removeButtonHandler;

	constructor({ id, products, user_id, deliveryAddress, createdAt, editedAt, name, surname, status, editButtonHandler, removeButtonHandler })
	{
		this.id = Number(id);
		this.products = products;
		this.user_id = Number(user_id);
		this.deliveryAddress = String(deliveryAddress);
		this.createdAt = new Date(Number(createdAt)*1000).toDateString();
		this.editedAt = new Date(Number(editedAt)*1000).toDateString();
		this.name = String(name);
		this.surname = String(surname);
		this.status = String(status);

		if (typeof editButtonHandler === 'function')
		{
			this.editButtonHandler = editButtonHandler;
		}

		if (typeof removeButtonHandler === 'function')
		{
			this.removeButtonHandler = removeButtonHandler;
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


		const productsColumn = this.createProductsColumn();
		productsColumn.classList.add('table__th', 'table__th_products')

		/*const userIdColumn = document.createElement('td');
		userIdColumn.classList.add('table__th', 'table__th_userId');
		userIdColumn.innerText = this.user_id;*/

		const deliveryAddressColumn = document.createElement('td');
		deliveryAddressColumn.classList.add('table__th', 'table__th_deliveryAddress');
		deliveryAddressColumn.innerText = this.deliveryAddress;

		const createdAtColumn = document.createElement('td');
		createdAtColumn.classList.add('table__th');
		createdAtColumn.innerText = this.createdAt;

		const editedAtColumn = document.createElement('td');
		editedAtColumn.classList.add('table__th');
		editedAtColumn.innerText = this.editedAt;

		const nameColumn = document.createElement('td');
		nameColumn.classList.add('table__th');
		nameColumn.innerText = this.name;

		const surnameColumn = document.createElement('td');
		surnameColumn.classList.add('table__th');
		surnameColumn.innerText = this.surname;

		const statusIdColumn = document.createElement('td');
		statusIdColumn.classList.add('table__th');
		statusIdColumn.innerText = this.status;

		const spinnerRemove = document.createElement('div');
		spinnerRemove.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRemove = document.createElement('span');
		spinnerLoadingRemove.innerText = 'Loading...';
		spinnerLoadingRemove.classList.add('visually-hidden');
		spinnerRemove.append(spinnerLoadingRemove);

		/*const spinnerRestore = document.createElement('div');
		spinnerRestore.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRestore = document.createElement('span');
		spinnerLoadingRestore.innerText = 'Loading...';
		spinnerLoadingRestore.classList.add('visually-hidden');
		spinnerRestore.append(spinnerLoadingRestore);*/

		const spinnerEdit = document.createElement('div');
		spinnerEdit.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingEdit = document.createElement('span');
		spinnerLoadingEdit.innerText = 'Loading...';
		spinnerLoadingEdit.classList.add('visually-hidden');
		spinnerEdit.append(spinnerLoadingEdit);

		const removeButton = document.createElement('button');
		removeButton.classList.add('table__button', 'table__button_delete');
		removeButton.id = String(this.id) + 'remove';
		removeButton.innerText = 'Удалить';
		removeButton.addEventListener('click', this.handleRemoveButtonClick.bind(this));
		removeButton.append(spinnerRemove);

		const editButton = document.createElement('button')
		editButton.classList.add('table__button', 'table__button_edit')
		editButton.innerText = 'Редактировать';
		editButton.id = String(this.id) + 'edit';
		editButton.addEventListener('click', this.handleEditButtonClick.bind(this));
		editButton.append(spinnerEdit);


		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton, removeButton,);

		trProduct.append(
			idColumn,
			/*userIdColumn,*/
			deliveryAddressColumn,
			statusIdColumn,
			createdAtColumn,
			editedAtColumn,
			nameColumn,
			surnameColumn,
			productsColumn,
			actionsColumn);
		return trProduct;
	}

	createProductsColumn()
	{
		const productsColumn = document.createElement('td');
		productsColumn.classList.add('dropdown_list');

		const dropdownButton = document.createElement('dropdown_button');
		dropdownButton.innerText = 'Показать продукты';
		dropdownButton.id = String(this.id) + 'dropdown';
		dropdownButton.addEventListener('click', this.showDropDownProducts.bind(this));

		const productRowContainer = document.createElement('div');
		productRowContainer.id = String(this.id) + 'productsRowContainer';
		productRowContainer.classList.add('productsRowContainer');

		const labelsContainer = document.createElement('ul');
		labelsContainer.id = 'productLabel';
		labelsContainer.classList.add('productLabel');
		const idProductLabel = document.createElement('label');
		idProductLabel.innerText = 'Id продукта';
		const quantityProductLabel = document.createElement('label');
		quantityProductLabel.innerText = 'Количество продукта';
		const priceProductLabel = document.createElement('label');
		priceProductLabel.innerText = 'Цена продукта';

		labelsContainer.append(idProductLabel, quantityProductLabel, priceProductLabel);

		productRowContainer.append(labelsContainer);

		this.products.forEach((product) => {
			const productRow = document.createElement('ul');
			productRow.id = 'productRow';
			productRow.classList.add('productRow');

			const productIdColumn = document.createElement('li')
			productIdColumn.classList.add();
			productIdColumn.innerText = product['itemId'];

			const productQuantitiesColumn = document.createElement('li')
			productQuantitiesColumn.classList.add();
			productQuantitiesColumn.innerText = product['quantities'];

			const productPriceColumn = document.createElement('li')
			productPriceColumn.classList.add();
			productPriceColumn.innerText = product['price'];

			productRow.append(productIdColumn, productQuantitiesColumn, productPriceColumn);
			productRowContainer.append(productRow);
		});
		productsColumn.append(dropdownButton, productRowContainer);
		return productsColumn;
	}

	showDropDownProducts()
	{
		let products = document.getElementById(String(this.id) + 'productsRowContainer');

		if (products.style.display === "block") {
			products.style.display = "none";
		} else {
			products.style.display = "block";
		}
	}
	/*window.onclick = function (event) {
	if (!event.target.matches('.dropdown_button')) {
		document.getElementById('courses_id')
			.style.display = "none";
	}*/

	handleRemoveButtonClick()
	{
		if (this.removeButtonHandler)
		{
			this.removeButtonHandler(this);
		}
	}

	handleEditButtonClick()
	{
		if (this.editButtonHandler)
		{
			this.editButtonHandler(this);
		}
	}
}
