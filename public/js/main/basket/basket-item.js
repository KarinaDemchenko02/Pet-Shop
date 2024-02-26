export class BasketItem
{
	id;
	title;
	price;
	imagePath;
	constructor({ id, title, price, imagePath })
	{
		this.id = Number(id);
		this.title = String(title);
		this.price = Number(price);
		this.imagePath = String(imagePath);
	}

	render()
	{
		return `
			<li class="basket__item" id="itemBasket${this.id}">
				<button id="item:${this.id}" class="basket__delete">
					<i class="basket__delete-icon material-icons">close</i>
				</button>
				<img class="basket__images" src="${this.imagePath}" alt="product">
				<h2 class="basket__heading-product">${this.title}</h2>
				<div class="basket__quantity">
					<button class="basket__btn-quantity basket__plus-btn">
						<i class="basket__plus-icon material-icons">add</i>
					</button>
					<input class="basket__input-number" type="text" name="name" value="1">
					<button class="basket__btn-quantity basket__minus-btn">
						<i class="basket__minus-icon material-icons">remove</i>
					</button>
				</div>
				<span class="basket__price">${this.price}₽</span>
			</li>
			`;
	}

}
