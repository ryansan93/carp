<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr class="cursor-p" onclick="branch.changeTabActive(this)" data-href="action" data-edit="" data-id="<?php echo $value['kode']; ?>">
			<td><?php echo $value['kode']; ?></td>
			<td><?php echo $value['nama']; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="2">Data tidak di temukan.</td>
	</tr>
<?php endif ?>