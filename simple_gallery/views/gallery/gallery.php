<!-- Gallery -->
<div id="gallery">
	<ul>
	<?php foreach ($designs as $index => $design) : ?>
		<li>
			<img src="/content/screenshots/<?= $design['screenshot'] ?>" title="<?= $design['title'] ?>" alt="<?= $design['title'] ?>" />
		</li>
	<?php endforeach; ?>
	</ul>
</div> <!-- End Gallery -->
