<?php
/**
 *
 */
?>
<div>
	main page
</div>
<div>
    <?php foreach ($this->getVariable('products') as $product): ?>
        <?php $product->display() ?> <br>
    <?php endforeach; ?>
</div>
