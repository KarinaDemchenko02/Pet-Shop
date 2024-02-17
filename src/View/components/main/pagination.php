<?php

$countShowPages = 4;

if (isset($_GET['page']))
{
	$activePage = (int)$_GET['page'];
}
else
{
	$activePage = 1;
}

$countPage = (int)$this->getVariable('countPage');

if ($countPage > 1)
{
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

	if ($end > $countPage)
	{
		$start -= ($end - $countPage);
		$end = $countPage;
		if ($start < 1)
		{
			$start = 1;
		}
	}
}
?>

<div class="pagination">
	<?php
	if ($activePage !== 1) { ?>

		<a class="pagination__button-switch pagination__button_return" href='/?<?= http_build_query(
			array_merge($_GET, ['page' => 1])
		) ?>'>Первая страница</a>
		<a class="pagination__button-switch pagination__button_next" href='/?<?= http_build_query(
			array_merge($_GET, ['page' => $activePage - 1])
		) ?>'>Назад</a>
	<?php
	} ?>
	<?php
	for ($p = $start; $p <= $end; $p++) { ?>
		<a class="pagination__button" href='/?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>'>
			<?= $p ?>
		</a>
	<?php
	} ?>
	<?php
	if ($activePage !== $countPage) { ?>
		<a class="pagination__button-switch pagination__button_next" href='/?<?= http_build_query(
			array_merge($_GET, ['page' => $activePage + 1])
		) ?>'>Вперед</a>
		<a class="pagination__button-switch pagination__button_return" href='/?<?= http_build_query(
			array_merge($_GET, ['page' => $countPage])
		) ?>'>Последняя страница</a>

	<?php
	} ?>
</div>
