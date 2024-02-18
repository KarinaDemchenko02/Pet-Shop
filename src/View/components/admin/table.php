<?php

$products = $this->getVariable('products');
$columns = $this->getVariable('columnsProducts');

$productList = [];
foreach ($products as $product)
{
	$tags = [];
	foreach ($product->tags as $tag)
	{
		$tags[] = $tag->title;
	}
	$productList[] =
		[
			'title' => $product->title,
			'description' => $product->description,
			'price' => $product->price,
			'id' => $product->id,
			'isActive' => (int) $product->isActive,
			'addedAt' => $product->addedAt,
			'editedAt' => $product->editedAt,
			'tags' =>implode(", ", $tags),
		];

}
?>

<div class="table__container">

	<div id="item-list">
	</div>

	<div class="delete__box">
		<?php $this->getVariable('delete')->display(); ?>
	</div>

</div>

<script type="module">
	import { ProductList } from "/js/admin/product/product-list.js";

	const mainProductList = new ProductList({
		attachToNodeId: 'item-list',
		items: <?= \Up\Util\Json::encode($productList) ?>,
		columns: <?= \Up\Util\Json::encode($columns) ?>,
	});

	mainProductList.render();
</script>
