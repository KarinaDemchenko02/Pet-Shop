<?php
$isLogin = $this->getVariable('isLogIn');
?>
<div class="main__container">
	<section class="form">
		<?php $this->getVariable('form')->display() ?>
	</section>
	<section class="basket">
		<?php $this->getVariable('basket')->display() ?>
	</section>
	<section class="special__offer">
		<ul class="special__offer__list">
			<?php foreach ($this->getVariable('specialOffersPreviewProducts') as $specialOffer): ?>
				<?= $specialOffer->display() ?>
			<?php endforeach; ?>
		</ul>
	</section>
</div>
<script type="module">
	import {Auth} from "/js/main/auth/auth.js";

	const auth = new Auth({
		attachToNodeId: 'form-auth',
		login: <?= \Up\Util\Json::encode($isLogin) ?>,
	})

	auth.render();
</script>