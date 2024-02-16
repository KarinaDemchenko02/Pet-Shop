<?php $productList = $this->getVariable('data'); ?>

<div class="table__container">
	<div id="item-list">
		<?php $this->getVariable('data')->display(); ?>
	</div>
	<div class="form__box">
		<?php $this->getVariable('form')->display(); ?>
	</div>
	<div class="delete__box">
		<?php $this->getVariable('delete')->display(); ?>
	</div>
</div>

<script type="module">
	import { ProductList } from "/js/admin/product/product-list.js";

	const mainProductList = new ProductList({
		attachToNodeId: 'item-list',
		items: <?= \Up\Util\Json::encode($productList) ?>
	});

	mainProductList.render();
</script>
