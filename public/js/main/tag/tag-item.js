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
		const itemTag = document.createElement('li');
		itemTag.classList.add('tags__item');

		const linkItem = document.createElement('a');
		linkItem.classList.add('tags__link');
		linkItem.id = 'tag:' + this.id;
		linkItem.innerText = this.title;
		linkItem.addEventListener('click', this.handleFilterTagButtonClick.bind(this));

		itemTag.append(linkItem);

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