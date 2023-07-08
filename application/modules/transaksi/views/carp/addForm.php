<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Code</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control kode" disabled="disabled" placeholder="Code">
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Kategori</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control kategori" data-required="1" placeholder="Kategori">
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Initiator</label>
	</div>
	<div class="col-xs-12" style="border: 1px solid black; border-radius: 5px; padding-bottom: 10px;">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Initiator</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control initiator_user" data-required="1">
				<option value="">-- Pilih Initiator --</option>
				<?php if ( !empty($user) ): ?>
					<?php foreach ($user as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>

		<div class="col-xs-12 no-padding">
			<label class="control-label">Initiator Divisi</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control initiator_divisi" data-required="1">
				<option value="">-- Pilih Initiator Divisi --</option>
				<?php if ( !empty($divisi) ): ?>
					<?php foreach ($divisi as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>

		<div class="col-xs-12 no-padding">
			<label class="control-label">Initiator Branch</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control initiator_branch" data-required="1">
				<option value="">-- Pilih Initiator Branch --</option>
				<?php if ( !empty($branch) ): ?>
					<?php foreach ($branch as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Recipient</label>
	</div>
	<div class="col-xs-12" style="border: 1px solid black; border-radius: 5px; padding-bottom: 10px;">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Recipient</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control recipient_user" data-required="1">
				<option value="">-- Pilih Recipient --</option>
				<?php if ( !empty($user) ): ?>
					<?php foreach ($user as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>

		<div class="col-xs-12 no-padding">
			<label class="control-label">Recipient Divisi</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control recipient_divisi" data-required="1">
				<option value="">-- Pilih Recipient Divisi --</option>
				<?php if ( !empty($divisi) ): ?>
					<?php foreach ($divisi as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>

		<div class="col-xs-12 no-padding">
			<label class="control-label">Recipient Branch</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control recipient_branch" data-required="1">
				<option value="">-- Pilih Recipient Branch --</option>
				<?php if ( !empty($branch) ): ?>
					<?php foreach ($branch as $key => $value): ?>
						<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
		</div>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Verified By</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control verified_by">
			<option value="">-- Verified By --</option>
			<?php if ( !empty($user) ): ?>
				<?php foreach ($user as $key => $value): ?>
					<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>
<div class="col-xs-6 no-padding" style="padding-right: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Stage</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control stage" data-required="1">
			<option value="">-- Stage --</option>
			<?php if ( !empty($stage) ): ?>
				<?php foreach ($stage as $key => $value): ?>
					<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>
<div class="col-xs-6 no-padding" style="padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Status</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control status" data-required="1">
			<option value="">-- Status --</option>
			<?php if ( !empty($status) ): ?>
				<?php foreach ($status as $key => $value): ?>
					<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<hr style="margin-top: 10px; margin-bottom: 10px;">
</div>
<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary col-xs-12" onclick="carp.save()"><i class="fa fa-save"></i> Save</button>
</div>