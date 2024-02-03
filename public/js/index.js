import AddBasket from './AddBasket.js';
import Tabs from './Tabs.js';

const buttonBuy = document.querySelectorAll('.product__buy');
const buttonRemove = document.querySelectorAll('.product__right-remove');

new AddBasket(buttonBuy, buttonRemove, 'product__bottom-content').addBasket();

if (document.getElementById("img-container")) {
    let options = {
        width: 400,
        height: 400,
    };

    new ImageZoom(document.getElementById("img-container"), options);
}


const btnsItem = document.querySelectorAll('.details__btn-item');
const blocksWithInfo = document.querySelectorAll('.details__info-items');

new Tabs(btnsItem, blocksWithInfo).tabs();