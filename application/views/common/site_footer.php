<? if ($this->device->isSmall()) { ?>
mobile footer menu

<? } ?>

	<!-- inline templates -->
	<?= $templates; ?>

	
	<!-- post load -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?
        // Load all postload JS
		if (isset($js)) {
			foreach($js as $url) {
				echo '<script src="'.$url.'"></script>'."\n";
			}
		}
	?>
</body>
</html>