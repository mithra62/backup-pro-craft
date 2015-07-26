$(document).ready(function() {
	
	//settings form
	var backup_type = '';
	if($("#auto_threshold").val() == "custom")
	{
		$("#auto_threshold_custom_wrap").show();
	}
		
	if($("#db_backup_method").val() == "mysqldump")
	{
		$("#mysqldump_command_wrap").show();
	}

	if($("#db_restore_method").val() == "mysql")
	{
		$("#mysqlcli_command_wrap").show();
	}				
	
	var def_assign = "0";
	$("#auto_threshold").change(function(){
		var new_assign = $("#auto_threshold").val();
		if(new_assign == def_assign || new_assign != "custom")
		{
			$("#auto_threshold_custom_wrap").hide();
			$("#auto_threshold_custom_wrap").val(new_assign);
		}
		else
		{
			$("#auto_threshold_custom_wrap").show();
		}
	});	

	var def_assign = "php";
	$("#db_backup_method").change(function(){
		var new_assign = $("#db_backup_method").val();
		if(new_assign == def_assign)
		{
			$("#mysqldump_command_wrap").hide();
		}
		else
		{
			$("#mysqldump_command_wrap").show();
		}
	});	

	$("#db_restore_method").change(function(){
		var new_assign = $("#db_restore_method").val();
		if(new_assign == def_assign)
		{
			$("#mysqlcli_command_wrap").hide();
		}
		else
		{
			$("#mysqlcli_command_wrap").show();
		}
	});
	//end settings form
	

	//now the testing cron 
	$(".test_cron").click(function (e) {
		
		e.preventDefault();
		var backup_type = $(this).attr("rel");
		var url = $(this).attr("href");
		var link = this;
		
		var image_id = "#animated_" + backup_type;
		$(image_id).show();
		$(link).hide();

		$.ajax({
			url: url,
			context: document.body,
			success: function(xhr){
				alert(" Cron: Complete");
				$(image_id).hide();
				$(link).show();
				clean_bp_errors(backup_type);
			},
			error: function(data, status, errorThrown) {
				alert(" Cron: Failed with status "+ data.status +"\n" +errorThrown );
				$(image_id).hide();
				$(link).show();									
			}
		});			
		
		return false;
		
	});	
	
	
});