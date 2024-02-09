export default class AddBasket {
	constructor(buttonBuy, buttonRemove, classContains) {
		this.buttonBuy = buttonBuy;
		this.buttonRemove = buttonRemove;
		this.classContains = classContains;
	}

	addBasket() {
		this.buttonBuy.forEach(bntBuy => {
			bntBuy.addEventListener('click', (event) => {
				this.clicked('add')
			})
		})

		this.buttonRemove.forEach(btnRemove => {
			btnRemove.addEventListener('click', () => {
				this.clicked('remove')
			})
		})
	}

	clicked(action) {
		let parentElement = event.target.parentElement;
		let parents;

		while (parentElement) {
			if (parentElement.classList.contains(this.classContains)) {
				parents = parentElement;
				break;
			}
			parentElement = parentElement.parentElement;
		}

		if (action === 'add') {
			parents.classList.add('clicked');
			return true;
		}

		if (action === 'remove') {
			parents.classList.remove('clicked');
			return true;
		}

		return false;
	}
}