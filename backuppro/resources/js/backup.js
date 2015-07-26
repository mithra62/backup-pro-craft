$(document).ready(function() {

	function clean_bp_errors(backup_type)
	{
		switch(backup_type)
		{
			case 'combined':
				$("#backup_pro_system_error_db_backup_state, #backup_pro_system_error_backup_state_db_backups").hide();
				$("#backup_pro_system_error_file_backup_state").hide();
			break;
			
			case 'file_backup':
				$("#backup_pro_system_error_file_backup_state, #backup_pro_system_error_backup_state_files_backups").hide();
			break;
			
			default:
			case 'db_backup':
				$("#backup_pro_system_error_db_backup_state, #backup_pro_system_error_backup_state_db_backups").hide();
			break;
		}
	}
	
	$("#_backup_direct").on("click", function(e) {
		$("#backup_running_details").show();
		$(this).hide();
	});
	
	/*
	//progressbar goodies
	$("#_backup_start").on("click", function(e) {
		
		$("#_backup_start_container").hide();
		$("#progress_bar_container").show();
		var kill_progress = false,
		backupProcess = new $.Deferred(), 
		url_base = $('#__url_base').val(),
		proc_url = $('#__backup_proc_url').val(),
		lang_backup_progress_bar_stop = $('#__lang_backup_progress_bar_stop').val();
		
		startBackup(backupProcess);
		backupProcess.progress(onProgressUpdate);
		backupProcess.fail(onBackupError);
		backupProcess.done(onBackupComplete);
		
		
		//Event Methods
		
		function onBackupComplete(data) {
			kill_progress = true;
			$('#progressbar').progressbar('option', 'value', 100);
			$('#active_item').html('');
			$('#total_items').html(data['total_items']);
			$('#active_item').html(data['msg']);
			$('#item_number').html(data['item_number']);
			$('div.heading h2.edit').html(lang_backup_progress_bar_stop);
			document.title = lang_backup_progress_bar_stop;
			$('#breadCrumb li:last').html(lang_backup_progress_bar_stop);
			$('#backup_instructions').hide();
			$("#backup_dashboard_menu").show();
			$("#_backup_download").show();
			var type = $("#__backup_type").val();
			if( type == 'backup_db' )
			{
				backup_type = 'db_backup';
			}
			else
			{
				backup_type = 'file_backup';
			}
			
			clean_bp_errors(backup_type);
		}
		
		function onProgressUpdate(data) {
			if(!data) return;
			progress = Math.floor(data['item_number']/data['total_items']*100);
			$('#progressbar').progressbar('option', 'value', progress);
			$('#total_items').html(data['total_items']);
			$('#active_item').html(data['msg']);
			$('#item_number').html(data['item_number']);
			if(data['total_items'] > 0 && data['item_number'] > 0 && data['item_number'] == data['total_items'])
			{
				$('div.heading h2.edit').html(lang_backup_progress_bar_stop);
				document.title = lang_backup_progress_bar_stop;
				$('#breadCrumb li:last').html(lang_backup_progress_bar_stop);
				$('#backup_instructions').hide();
				$("#backup_dashboard_menu").show();
				$("#_backup_download").show();
			}
		}
		
		function onBackupError(data) {
			kill_progress = true;
			alert('Error encountered. Unable to complete backup');
			console.error(data)
		}

		function startBackup(dfd){
			var kp = kill_progress;
		
			setTimeout(function(){ 
				
			
				$.ajax({
					url: proc_url,
					cache: false,
					dataType: 'html',
					error: function(data){
						dfd.reject(data);
					},
					success: function(data) {
						var _dfd = dfd;
						$.ajax({
							url: url_base+'progress',
							cache: false,
							dataType: 'json',
							error: function(data){
								_dfd.reject(data);
							},
							success: function(data) {
								onBackupComplete(data);
							}
						});
					}
				});
		
				setTimeout(updateLoop, 500);
				dfd.progress(function(){
					progress = $('#progressbar').progressbar('option','value');
		
					if (progress < 100 && !kp) {
						setTimeout(updateLoop, 1000);
					}
				});
			}, 1000);
		
			return dfd;
		}
		
		function updateLoop() {
			var _backupProcess = backupProcess,
				progress;
		
			$.ajax({
				url: url_base+'progress',
				cache: false,
				dataType: 'json',
				error: function(data){
					_backupProcess.reject(data);
				},
				success: function(data) {
					_backupProcess.notify(data)
				}
			});
		
		}	
	});
	
	*/
	
});	