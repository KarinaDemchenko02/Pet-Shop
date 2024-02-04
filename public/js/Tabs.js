export default class Tabs
{
    constructor(buttons, blockInfo) {
        this.buttons = buttons;
        this.blockInfo = blockInfo;
    }

    tabs()
    {
        this.buttons.forEach(btn =>
        {
            btn.addEventListener('click', () =>
            {
                let currentBtn = btn;
                let tabData = currentBtn.getAttribute('data-tab');
                let tabInfo = document.querySelector(tabData);

                if (!currentBtn.classList.contains('is-active'))
                {

                    this.buttons.forEach(btn =>
                    {
                        btn.classList.remove('is-active');
                    })

                    this.blockInfo.forEach(block =>
                    {
                        block.style.display = 'none';
                    })

                    currentBtn.classList.add('is-active');
                    tabInfo.style.display = 'block';
                }
            })
        });
    }
}
