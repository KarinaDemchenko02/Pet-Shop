export class ProductItem
{
	id;
	title;
	description;
	price;
	imagePath;
	constructor({ id, title, description, price, imagePath })
	{
		this.id = Number(id);
		this.title = String(title);
		this.description = String(description);
		this.price = Number(price);
		this.imagePath = String(imagePath);
	}

	render()
	{
		return `
			<li class="product__wrapper">
				<div class="product__content">
					<a href="/product/${this.id}/">
						<img class="product__images" src="${this.imagePath}" alt="product">
					</a>
					<div class="product__bottom-content">
						<div class="product__left-content">
							<div class="product__details">
								<h2 class="product__heading">${this.title}</h2>
								<p class="product__price">${this.price} ₽</p>
							</div>
							<form method="post" action="/addToBasket/${this.id}/">
								<button class="product__buy">
									<i class="material-icons product__icon-buy">add_shopping_cart</i>
								</button>
							</form>
						</div>
						<div class="product__right-content">
							<div class="product__right-done">
								<i class="material-icons product__done-icon">done</i>
							</div>
							<div class="product__details">
								<h2 class="product__heading product__heading-details">${this.title}</h2>
								<p class="product__price product__price-details">Товар в корзине</p>
							</div>
							<div class="product__right-remove">
								<i class="material-icons product__remove-icon">clear</i>
							</div>
						</div>
					</div>
				</div>
				<div class="product__information">
					<div class="product__container-icon">
						<i class="product__icon material-icons">info_outline</i>
					</div>
					<div class="product__more-content">
						<p class="product__info">
							${this.description}
						</p>
					</div>
				</div>
			</li>
			`;
	}
}