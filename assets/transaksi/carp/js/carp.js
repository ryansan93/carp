var carp = {
	start_up: function(){
		carp.getLists();
		carp.settingUp();
	}, // end - start_up

	settingUp: function() {
		$('.initiator_user').select2();
		$('.initiator_divisi').select2();
		$('.initiator_branch').select2();
		$('.recipient_user').select2();
		$('.recipient_divisi').select2();
		$('.recipient_branch').select2();
		$('.verified_by').select2();
		$('.stage').select2();
		$('.status').select2();
	}, // end - settingUp

	getLists : function(){
		$.ajax({
			url : 'transaksi/CARP/getLists',
			data : {},
			dataType : 'HTML',
			type : 'GET',
			beforeSend : function(){ showLoading(); },
			success : function(html){
				$('table.tbl_carp tbody').html(html);

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

            carp.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/CARP/loadForm',
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

                carp.settingUp();
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data CARP ?', function(result){
				if (result) {
					var params = {
						'kategori' : $('.kategori').val(),
						'initiator_user' : $('.initiator_user').select2('val'),
						'initiator_divisi' : $('.initiator_divisi').select2('val'),
						'initiator_branch' : $('.initiator_branch').select2('val'),
						'recipient_user' : $('.recipient_user').select2('val'),
						'recipient_divisi' : $('.recipient_divisi').select2('val'),
						'recipient_branch' : $('.recipient_branch').select2('val'),
						'verified_by' : $('.verified_by').select2('val'),
						'stage' : $('.stage').select2('val'),
						'status' : $('.status').select2('val')
					};

					$.ajax({
						url : 'transaksi/CARP/save',
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
									// carp.getLists();
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
			bootbox.confirm('Apakah anda yakin ingin meng-ubah data CARP ?', function(result){
				if (result) {
					var params = {
						'kode' : $('.kode').val(),
						'kategori' : $('.kategori').val(),
						'initiator_user' : $('.initiator_user').select2('val'),
						'initiator_divisi' : $('.initiator_divisi').select2('val'),
						'initiator_branch' : $('.initiator_branch').select2('val'),
						'recipient_user' : $('.recipient_user').select2('val'),
						'recipient_divisi' : $('.recipient_divisi').select2('val'),
						'recipient_branch' : $('.recipient_branch').select2('val'),
						'verified_by' : $('.verified_by').select2('val'),
						'stage' : $('.stage').select2('val'),
						'status' : $('.status').select2('val')
					};

					$.ajax({
						url : 'transaksi/CARP/edit',
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
									// carp.getLists();
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

	delete: function(elm) {
		bootbox.confirm('Apakah anda yakin ingin menghapus data CARP ?', function(result){
			if ( result ) {
				var kode = $(elm).attr('data-kode');

				var params = {
					'kode': kode
				};

				$.ajax({
					url : 'transaksi/CARP/delete',
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

carp.start_up();