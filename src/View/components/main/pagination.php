<?php

$url = $_SERVER['REQUEST_URI'];
$url = explode('?', $url)[0];

$page = $this->getVariable('products');

if (empty($page))
{
	exit();
}

$nextPage = $this->getVariable('nextPage');

$limit = \Up\Util\Configuration::getInstance()->option('NUMBER_OF_PRODUCTS_PER_PAGE');

$countShowPages = 2;

if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
{
	$activePage = (int)$_GET['page'];
}
else
{
	$activePage = 1;
}

$left = $activePage - 1;

if ($left < floor($countShowPages / 2))
{
	$start = 1;
}
else
{
	$start = $activePage - floor($countShowPages / 2);
}

$end = $start + $countShowPages - 1;

if (empty($nextPage))
{
	$start -= ($end - $activePage);
	$end = $activePage;
	if ($start < 1)
	{
		$start = 1;
	}
}

?>

<div class="pagination">
	<?php
	if ($activePage !== 1)
	{ ?>
		<a class="pagination__button-switch pagination__button_next" href='<?= $url . '?' . http_build_query(
			array_merge($_GET, ['page' => $activePage - 1])
		) ?>'>Назад</a>
		<?php
	} ?>
	<?php
	for ($p = $start; $p <= $end; $p++)
	{ ?>
		<a class="pagination__button" href='<?= $url . '?' . http_build_query(array_merge($_GET, ['page' => $p])) ?>'>
			<?= $p ?>
		</a>
		<?php
	} ?>
	<?php
	if (!empty($nextPage))
	{ ?>
		<a class="pagination__button-switch pagination__button_next" href='<?= $url . '?' . http_build_query(
			array_merge($_GET, ['page' => $activePage + 1])
		) ?>'>Вперед</a>
		<?php
	} ?>
</div>
