<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $no = 1; ?>
	<?php foreach ($data as $key => $value): ?>
		<tr class="cursor-p" onclick="carp.changeTabActive(this)" data-href="action" data-edit="" data-id="<?php echo $value['kode']; ?>">
			<td><?php echo $no; ?></td>
			<td><?php echo $value['created_date']; ?></td>
			<td><?php echo $value['kode']; ?></td>
			<td><?php echo $value['kategori']; ?></td>
			<td><?php echo $value['inama_user']; ?></td>
			<td><?php echo $value['inama_divisi']; ?></td>
			<td><?php echo $value['inama_branch']; ?></td>
			<td><?php echo $value['rnama_user']; ?></td>
			<td><?php echo $value['rnama_divisi']; ?></td>
			<td><?php echo $value['rnama_branch']; ?></td>
			<td><?php echo $value['vnama']; ?></td>
			<td><?php echo $value['due_date']; ?></td>
			<td>-</td>
			<td><?php echo $value['status_date']; ?></td>
			<td><?php echo $value['stage']; ?></td>
			<td><?php echo $value['status']; ?></td>
		</tr>
		<?php $no++; ?>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="16">Data tidak di temukan.</td>
	</tr>
<?php endif ?>