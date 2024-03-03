export class TagItem
{
	id;
	title;
	filterTagButtonHandler;
	constructor({ id, title, filterTagButtonHandler })
	{
		this.id = Number(id);
		this.title = String(title);

		if (typeof filterTagButtonHandler === 'function')
		{
			this.filterTagButtonHandler = filterTagButtonHandler;
		}

	}

	render()
	{
		let currentUrl = window.location.href;
		let newUrl = new URL(currentUrl);
		let tags = newUrl.searchParams.get('tag');

		const itemTag = document.createElement('li');
		itemTag.classList.add('tags__item');

		const inputItem = document.createElement('input');
		inputItem.classList.add('tags__checkbox')
		inputItem.type = "checkbox";
		inputItem.id = 'tag:' + this.id;
		inputItem.name = 'tag:' + this.id;

		if (tags && tags.includes(this.id)) {
			inputItem.checked = true;
		}

		const labelInput = document.createElement('label');
		labelInput.textContent = this.title;
		labelInput.htmlFor = 'tag:' + this.id;


		inputItem.addEventListener('click', this.handleFilterTagButtonClick.bind(this));

		itemTag.append(inputItem, labelInput);

		return itemTag;
	}

	handleFilterTagButtonClick()
	{
		if (this.filterTagButtonHandler)
		{
			this.filterTagButtonHandler(this);
		}
	}
}