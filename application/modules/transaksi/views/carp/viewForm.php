<div class="col-xs-12 no-padding">
	<div class="col-xs-1 no-padding">
		<label class="control-label">Code</label>
	</div>
	<div class="col-xs-11 no-padding">
		<label class="control-label">: <?php echo $data['kode']; ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-1 no-padding">
		<label class="control-label">Kategori</label>
	</div>
	<div class="col-xs-11 no-padding">
		<label class="control-label">: <?php echo $data['kategori']; ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-top: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Initiator</label>
	</div>
	<div class="col-xs-12" style="border: 1px solid black; border-radius: 5px; padding-top: 10px; padding-bottom: 10px;">
		<div class="col-xs-2 no-padding">
			<label class="control-label">Initiator</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['inama_user']; ?></label>
		</div>

		<div class="col-xs-2 no-padding">
			<label class="control-label">Initiator Divisi</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['inama_divisi']; ?></label>
		</div>

		<div class="col-xs-2 no-padding">
			<label class="control-label">Initiator Branch</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['inama_branch']; ?></label>
		</div>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-top: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Recipient</label>
	</div>
	<div class="col-xs-12" style="border: 1px solid black; border-radius: 5px; padding-top: 10px; padding-bottom: 10px;">
		<div class="col-xs-2 no-padding">
			<label class="control-label">Recipient</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['rnama_user']; ?></label>
		</div>

		<div class="col-xs-2 no-padding">
			<label class="control-label">Recipient Divisi</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['rnama_divisi']; ?></label>
		</div>

		<div class="col-xs-2 no-padding">
			<label class="control-label">Recipient Branch</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo $data['rnama_branch']; ?></label>
		</div>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-top: 10px;">
	<div class="col-xs-1 no-padding">
		<label class="control-label">Verified By</label>
	</div>
	<div class="col-xs-11 no-padding">
		<label class="control-label">: <?php echo !empty($data['vnama']) ? $data['vnama'] : '-'; ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-1 no-padding">
		<label class="control-label">Stage</label>
	</div>
	<div class="col-xs-11 no-padding">
		<label class="control-label">: <?php echo $data['stage']; ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-1 no-padding">
		<label class="control-label">Status</label>
	</div>
	<div class="col-xs-11 no-padding">
		<label class="control-label">: <?php echo $data['status']; ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<hr style="margin-top: 10px; margin-bottom: 10px;">
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="btn btn-danger col-xs-12 not-update" onclick="carp.delete(this)" data-kode="<?php echo $data['kode']; ?>"><i class="fa fa-trash"></i> Delete</button>
	</div>
	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="btn btn-primary col-xs-12 not-update" onclick="carp.changeTabActive(this)" data-href="action" data-edit="edit" data-id="<?php echo $data['kode']; ?>"><i class="fa fa-edit"></i> Update</button>
	</div>
</div>