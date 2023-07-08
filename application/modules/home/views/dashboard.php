<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-xs-6 no-padding" style="padding-right: 5px;">
			<div class="panel-body no-padding">
				<fieldset>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-6 no-padding">
							<label class="control-label">KOLOM STATUS</label>
						</div>
						<div class="col-xs-6 no-padding">
							<button type="button" class="btn btn-default pull-right" onclick="home.getDataStatus()"><i class="fa fa-refresh"></i></button>
						</div>
					</div>
					<div class="col-xs-12 no-padding">
						<hr style="margin-top: 10px; margin-bottom: 10px;">
					</div>
					<div class="col-xs-12 no-padding">
						<canvas id="myChartStatus"></canvas>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="padding-left: 5px;">
			<div class="panel-body no-padding">
				<fieldset>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-6 no-padding">
							<label class="control-label">KOLOM STAGE</label>
						</div>
						<div class="col-xs-6 no-padding">
							<button type="button" class="btn btn-default pull-right" onclick="home.getDataStage()"><i class="fa fa-refresh"></i></button>
						</div>
					</div>
					<div class="col-xs-12 no-padding">
						<hr style="margin-top: 10px; margin-bottom: 10px;">
					</div>
					<div class="col-xs-12 no-padding" id="myChartStage">
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>