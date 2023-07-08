<?php foreach ($data as $key => $value): ?>
	<div class="col-xs-3 no-padding" style="padding-left: 5px; padding-right: 5px; padding-bottom: 10px;">
		<div class="col-xs-12 no-padding" style="border: 1px solid <?php echo $value['color']; ?>;">
			<div class="col-xs-12 no-padding" style="background: <?php echo $value['color']; ?>; padding: 5px;">
				<div class="col-xs-12 no-padding text-center">
					<b><?php echo $value['nama']; ?></b>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding text-center" style="padding: 5px;">
					<b><?php echo $value['total']; ?></b>
				</div>
			</div>
		</div>
	</div>
<?php endforeach ?>