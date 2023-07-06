<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Tgl Awal</label>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date datetimepicker" name="startDate" id="StartDate">
					        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>

				<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Tgl Akhir</label>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date datetimepicker" name="endDate" id="EndDate">
					        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="hutang.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<?php echo $report_hutang; ?>
			</div>
		</form>
	</div>
</div>