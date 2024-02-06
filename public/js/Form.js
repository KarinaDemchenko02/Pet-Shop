export default class Form
{
    constructor(buttonOpen, buttonClose, content) {
        this.buttonOpen = buttonOpen
        this.buttonClose = buttonClose
        this.content = content
    }

    open()
    {
        this.template(this.buttonOpen,'add');
    }

    close()
    {
        this.template(this.buttonClose,'remove');
    }

    openRight()
    {
        this.buttonOpen.addEventListener('click', () => {
            this.content.style.right  = '-134%';
        })
    }

    closeRight()
    {
        this.buttonClose.addEventListener('click', () => {
            this.content.style.right  = '-60%';
        })
    }

    template(button, option)
    {
        button.addEventListener('click', () => {
            if (option === 'add')
            {
                this.content.classList.add('open');
                document.querySelector('.body').classList.add('no-scroll');
            }
            else if(option === 'remove')
            {
                this.content.classList.remove('open');
                document.querySelector('.body').classList.remove('no-scroll');
            }

        })
    }
}