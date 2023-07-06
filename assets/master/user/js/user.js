var user = {
	start_up: function(){
		user.getLists();
	}, // end - start_up

	getLists : function(){
		$.ajax({
			url : 'master/User/getLists',
			data : {},
			dataType : 'HTML',
			type : 'GET',
			beforeSend : function(){ showLoading(); },
			success : function(html){
				$('table.tbl_user tbody').html(html);

				hideLoading();
			}
		});
	}, // end - getLists

	changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        var edit = $(elm).data('edit');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');

            user.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'master/User/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);
            },
        });
    }, // end - loadForm

    update: function() {
    	var div = $('div#action');

    	$(div).find('input.nama').removeAttr('disabled');

    	$(div).find('button.update').removeClass('hide');
    	$(div).find('button.not-update').addClass('hide');
    }, // end - update

    cancel: function() {
    	var div = $('div#action');

    	$(div).find('input.nama').attr('disabled', 'disabled');

    	$(div).find('button.update').addClass('hide');
    	$(div).find('button.not-update').removeClass('hide');
    }, // end - cancel

	save: function () {
		var err = 0;
		$.map( $('[data-required=1]'), function(ipt){
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			};
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin menyimpan data user ?', function(result){
				if (result) {
					var nama_user = $('input.nama').val();

					var params = {
						'nama_user' : nama_user
					};

					$.ajax({
						url : 'master/User/save',
			            type: 'post',
						dataType: 'json',
			            data: {
			            	'params': params
			            },
						beforeSend : function(){
							showLoading('Proses simpan data . . .');
						},
						success : function(data){
							hideLoading();
							if ( data.status == 1 ) {
								bootbox.alert(data.message, function(){
									location.reload();
									// user.getLists();
									// bootbox.hideAll();
								});
							} else {
								bootbox.alert(data.message);
							}
						}
					});
				};
			});
		};
	}, // end - save

	edit: function () {
		var err = 0;
		$.map( $('[data-required=1]'), function(ipt){
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			};
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin meng-ubah data user ?', function(result){
				if (result) {
					var kode = $('input.kode').val();
					var nama_user = $('input.nama').val();

					var params = {
						'kode': kode,
						'nama_user' : nama_user
					};

					$.ajax({
						url : 'master/User/edit',
			            type: 'post',
						dataType: 'json',
			            data: {
			            	'params': params
			            },
						beforeSend : function(){
							showLoading('Proses simpan data . . .');
						},
						success : function(data){
							hideLoading();
							if ( data.status == 1 ) {
								bootbox.alert(data.message, function(){
									location.reload();
									// user.getLists();
									// bootbox.hideAll();
								});
							} else {
								bootbox.alert(data.message);
							}
						}
					});
				};
			});
		};
	}, // end - edit

	delete: function() {
		bootbox.confirm('Apakah anda yakin ingin menghapus data user ?', function(result){
			if ( result ) {
				var kode = $('input.kode').val();

				var params = {
					'kode': kode
				};

				$.ajax({
					url : 'master/User/delete',
					dataType: 'json',
					type: 'post',
					data: {
						'params' : params
					},
					beforeSend : function(){
						showLoading();
					},
					success : function(data){
						hideLoading();
						if ( data.status == 1 ) {
							bootbox.alert(data.message, function(){
								location.reload();
							});
						} else {
							bootbox.alert(data.message);
						}
					}
				});
			};
		});

	}, // end - delete
};

user.start_up();