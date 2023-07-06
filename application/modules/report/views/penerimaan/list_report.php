<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_total = 0; ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr>
			<td class="text-center"><?php echo tglIndonesia($v_data['tgl_terima'], '-', ' '); ?></td>
			<td class="text-center"><?php echo $v_data['kode_terima']; ?></td>
			<td class="text-center"><?php echo $v_data['po_no']; ?></td>
			<td><?php echo $v_data['supplier']; ?></td>
			<td><?php echo $v_data['nama_gudang']; ?></td>
			<td><?php echo $v_data['nama_item']; ?></td>
			<td><?php echo $v_data['coa']; ?></td>
			<td><?php echo $v_data['satuan']; ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['jumlah_terima']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['harga']); ?></td>
			<?php $total = $v_data['jumlah_terima'] * $v_data['harga']; ?>
			<?php $grand_total += $total; ?>
			<td class="text-right"><?php echo angkaDecimal($total); ?></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="10"><b>TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="11">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>