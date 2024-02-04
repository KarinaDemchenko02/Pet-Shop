<div class="main__container container">
    <section class="tags">
        <div class="tags__container">
            <h2 class="tags__heading">Теги:</h2>
            <ul class="tags__list">
                <li class="tags__item">
                    <a class="tags__link" href="#">Test</a>
                </li>
                <li class="tags__item">
                    <a class="tags__link" href="#">Test</a>
                </li>
            </ul>
        </div>
    </section>
    <section class="product">
        <ul class="product__list">
			<?php foreach ($this->getVariable('products') as $product): ?>

				<?php $product->display() ?>

			<?php endforeach; ?>
        </ul>
    </section>
</div>
