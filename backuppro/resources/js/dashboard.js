$(document).ready(function() {

	$('#NewStorageDropdown').change(function(){
		window.location = $("#NewStorageDropdown").val();
	});

	//check all checkboxes
	$(".toggle_all_db").toggle(
		function(){
			$("input.toggle_db").each(function() {
				this.checked = true;
			});
		}, function (){
			var checked_status = this.checked;
			$("input.toggle_db").each(function() {
				this.checked = false;
			});
		}
	);

	$(".toggle_all_files").toggle(
		function(){
			alert('fdsa');
			$("input.toggle_files").each(function() {
				this.checked = true;
			});
		}, function (){
			var checked_status = this.checked;
			$("input.toggle_files").each(function() {
				this.checked = false;
			});
		}
	);
	//end checkboxes

	//$('#existing_backups').dragCheck('td');
	//alert('fdsa');
	
	//backup note editable
	$(".bp_editable").on("click", function(e) {
		
		var file_id = "#note_"+$(this).attr("rel");
		var note_div = "#note_div_"+$(this).attr("rel");
		var note_html = "#note_div_"+$(this).attr("rel");
		var backup_type = $(file_id).attr("data-backup-type");
		var def_value = $(file_id).val();
		
		//first, prevent using Enter to submit the parent form
		$(file_id).bind("keypress", function(e) {
			  var code = e.keyCode || e.which; 
			  if (code  == 13) 
			  {               
			    e.preventDefault();
			    bp_save_note(note_div, file_id, backup_type);
			    return false;
			  }
		});	
		
		$(document).keyup(function(e) {
			  if (e.keyCode == 27) { 
					$(note_div).html($(note_html).html()).show();
					$(file_id).val(def_value);
					$(file_id).hide();
			  }   // esc
		});		

		//now do first display
		$(this).hide();
		$(file_id).show();
		$(file_id).focus();
		$(file_id).on("blur", function(e) {
			$(note_div).html($(note_html).html()).show();
			$(file_id).hide();
		});
	});
	//end backup note editable
	
});	