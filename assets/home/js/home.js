var myChartStatus = null;

var home = {
	startUp: function () {
		home.getDataStatus();
		home.getDataStage();
	},  // end - startUp

	getDataStatus: function () {
		var content = $('#myChartStatus');

		$.ajax({
            url: 'home/Home/getDataStatus',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { 
            	App.showLoaderInContent(content);
            	// showLoading(); 
           	},
            success: function(data) {
                App.showLoaderInContent(content);
                // hideLoading();
                if ( data.status == 1 ) {
                	home.chartStatus(data.content.list_status);
                }
            }
        });
	}, // end - getDataStatus

	chartStatus: function (list_status) {
		var backgroundColor = [
			'rgb(255, 99, 132)',
			'rgb(54, 162, 235)',
			'rgb(255, 205, 86)'
		];

		var labels = [];
		var jumlah = [];
		for (var i = 0; i < list_status.length; i++) {
			labels[i] = list_status[i]['nama'];
			jumlah[i] = list_status[i]['total'];
		}

		if ( !empty(myChartStatus) ) {
			myChartStatus.destroy();
		}

		myChartStatus = new Chart("myChartStatus", {
			type: "doughnut",
			data: {
				labels: labels,
				datasets: [{
					data: jumlah,
					backgroundColor: backgroundColor,
					hoverOffset: 4
				}]
			},
			options: {
				aspectRatio: 3
			},
		});
	}, // end - chartStatus

	getDataStage: function () {
		var content = $('#myChartStage');

		$.ajax({
            url: 'home/Home/getDataStage',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
            	App.showLoaderInContent(content);
            	// showLoading(); 
            },
            success: function(data) {
                App.showLoaderInContent(content);
                // hideLoading();
                if ( data.status == 1 ) {
                	$('#myChartStage').html( data.content.list_stage );
                }
            }
        });
	}, // end - getDataPenjualan

	tesChart: function () {
		// var xValues = [1, 2, 3, 4, 5, 6, 7];
		// var yValues = [0, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000];

		new Chart("myChartStatus", {
			type: "doughnut",
			data: {
				labels: [
					'Red',
					'Blue',
					'Yellow'
				],
				datasets: [{
					label: 'My First Dataset',
					data: [300, 50, 100],
					backgroundColor: [
						'rgb(255, 99, 132)',
						'rgb(54, 162, 235)',
						'rgb(255, 205, 86)'
					],
					hoverOffset: 4
				}]
			},
			options: {
				aspectRatio: 3
			},
		});
	}, // end - tesChart
};

home.startUp();