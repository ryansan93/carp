<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Kode</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control kode" disabled="disabled" placeholder="Kode (Generate otomatis terisi saat simpan data)" value="<?php echo $data['kode']; ?>">
	</div>
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control nama" data-required="1" placeholder="Nama" value="<?php echo $data['nama']; ?>" disabled="disabled">
	</div>
</div>
<div class="col-xs-12 no-padding">
	<hr style="margin-top: 10px; margin-bottom: 10px;">
</div>
<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="btn btn-danger col-xs-12 not-update" onclick="branch.delete()"><i class="fa fa-trash"></i> Delete</button>
		<button type="button" class="btn btn-danger col-xs-12 update hide" onclick="branch.cancel()"><i class="fa fa-times"></i> Cancel</button>
	</div>
	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="btn btn-primary col-xs-12 not-update" onclick="branch.update()"><i class="fa fa-edit"></i> Update</button>
		<button type="button" class="btn btn-primary col-xs-12 update hide" onclick="branch.edit()"><i class="fa fa-edit"></i> Save Change</button>
	</div>
</div>