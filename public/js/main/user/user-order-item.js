export class OrderItem
{
	id;
	name;
	path;
	price;
	quantities;
	status;

	constructor({ id, name, path, price, quantities, status})
	{
		this.id = Number(id)
		this.name = String(name);
		this.path = String(path);
		this.price = Number(price);
		this.quantities = Number(quantities);
		this.status = String(status);
	}

	render()
	{
		const item = document.createElement('li');
		item.classList.add('account__item');

		const itemHeading = document.createElement('h3');
		itemHeading.classList.add('account__item-heading');
		itemHeading.innerText = this.name;

		const orderImage = document.createElement('img');
		orderImage.classList.add('account__order-image');
		orderImage.src = this.path;
		orderImage.alt = 'Товар: ' + this.id;

		const orderPrice = document.createElement('span');
		orderPrice.classList.add('account__order-price');
		orderPrice.innerText = this.price;

		const orderStatus = document.createElement('span');
		orderStatus.classList.add('account__order-status');
		orderStatus.innerText = this.status;

		const orderQuantities = document.createElement('span');
		orderQuantities.classList.add('account__order-quantities');
		orderQuantities.innerText = this.quantities;

		item.append(itemHeading, orderImage, orderPrice, orderStatus, orderQuantities);

		return item;
	}
}
