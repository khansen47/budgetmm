$(document).ready(function() {
	$(".income-th, .expense-th").live("click", function() {
		var cat_id = $(this).attr("id");
		$.ajax({
			type: "POST",
			url: "ajax/income_details.php",
			data: {"cat_id":cat_id},
			success: function(data){
				$(".details-expand").html(data);
				$("#quick-type option[id='"+cat_id+"']").prop("selected", true);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#expenses-cats span").live("click", function() {
		var cat_id = $(this).attr("cat-id");
		$.ajax({
			type: "POST",
			url: "ajax/cat_edit.php",
			data: {"cat_id":cat_id},
			success: function(data){
				$("#category-edit").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#quick-type").change(function() {
		var cat_id = $("#quick-type option:selected").attr("id");
		$.ajax({
			type: "POST",
			url: "ajax/income_details.php",
			data: {"cat_id":cat_id},
			success: function(data){
				$(".details-expand").html(data);
				$("#quick-type option[id='"+cat_id+"']").prop("selected", true);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#year-review").bind("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/year_review.php",
			data: "",
			success: function(data){
				$("#content").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#all-years").bind("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/all_years.php",
			data: "",
			success: function(data){
				$("#content").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#all-items").bind("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/all_items.php",
			data: "",
			success: function(data){
				$("#content").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#month_report").bind("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/month_report.php",
			data: "",
			success: function(data){
				$("#content").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#manage-cats").bind("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/manage_cats.php",
			data: "",
			success: function(data){
				$("#content").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$( "#manage_auto" ).bind( "click", function()
	{
		$.ajax( 
		{
			type: "POST",
			url: "ajax/manage_auto.php",
			data: "",
			success: function(data)
			{
				$("#content").html(data);
			},
			error: function()
			{
				alert("error");
			}
		} );
	} );

	$("#man-cat span").live("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/year_review_cat.php",
			data: {"cat_id":$(this).attr("cat-id"), "year":$("#year_review_select").val()},
			success: function(data){
				$("#man-cat-inside").html(data);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("input:date").dateinput({ trigger: true, format: 'mmmm dd, yyyy'});

	$("#quick-add-submit").live("click", function() {
		var category = $("#quick-type option:selected").attr("id");
		var amount = parseFloat($("#quick-amount").val());
		var date = $("#quick-date").val();
		var comment = $("#quick-comment").val();
		var errors = "";

		if (category == undefined) {
			errors += "Please select a valid Category\n";
		}

		if (isNaN(amount) || amount <= 0) {
			errors += "Please enter an appropriate amount\n";
		}
		var d=new Date();
		var month=new Array(12);
		month[0]="January";
		month[1]="February";
		month[2]="March";
		month[3]="April";
		month[4]="May";
		month[5]="June";
		month[6]="July";
		month[7]="August";
		month[8]="September";
		month[9]="October";
		month[10]="November";
		month[11]="December";

		// if (date.split(' ')[0] != month[d.getMonth()]) {
			// errors += "Please select a date that is in "+month[d.getMonth()];
		// }

		if (errors != "") {
			alert(errors);
		} else {
			$.ajax({
				type: "POST",
				url: "ajax/quick_add.php",
				data: $.param({"category":category, "amount":amount, "date":date, "comment":comment}),
				success: function(data){
					$.ajax({
						type: "POST",
						url: "index_content.php",
						data: "",
						success: function(data){
							$("#content_details").html(data);
							$("#quick-type").val($("#quick-type  option:first").val());
							$("#quick-amount").val("");
							$("#quick-comment").val("");
							$("#quick-report").text("Successfully added item");
							$("#quick-report").css("color", "#8AB381");
						}, error: function() {
							alert("error");
						}
					});

					$.ajax({
						type: "POST",
						url: "ajax/income_details.php",
						data: {"cat_id":category},
						success: function(data) {
							$(".details-expand").html(data);
							$("#quick-type option[id='"+category+"']").prop("selected", true);
						}
					});
				},
				error: function(){
					alert("error");
				}
			});
		}
	});

	$("#month-select").bind("change", function() {
		$.ajax({
			type: "POST",
			url: "ajax/change_month.php",
			data: {"month":$(this).val()},
			success: function(data){
				location.reload();
				/* TEST TO SEE IF THIS FUNCTIONS BETTER
				$.ajax({
					type: "POST",
					url: "index.php",
					data: "",
					success: function(data){
						$("body").html(data);
					}
				});*/
			}
		});
	});

	$("#year-select").bind("change", function() {
		$.ajax({
			type: "POST",
			url: "ajax/change_year.php",
			data: {"year":$(this).val()},
			success: function(data){
				location.reload();
				/* TEST TO SEE IF THIS FUNCTIONS BETTER
				$.ajax({
					type: "POST",
					url: "index.php",
					data: "",
					success: function(data){
						$("body").html(data);
					}
				});*/
			}
		});
	});

	$("#year_review_select").live("change", function() {
		$.ajax({
			type: "POST",
			url: "ajax/year_review.php",
			data: {"year":$(this).val()},
			success: function(data){
				$("#content").html(data);
			}
		});
	});

	$("#edit-item").live("click", function() {
		if ($("#edit-item-cat_id").length) {
			alert("Only edit one item at a time please");
			return false;
		}

		var item_id 	= $(this).attr("item-id");
		var parent_tr 	= $(this).parent("tr");
		var cat_id 		= $(this).parents("table").attr("cat-id");
		var count 		= 1;
		var local_html 	= "";

		parent_tr.append("<td><select id='tmp_cat_dd_option'></select></td>");
		$("#quick-type option").clone().appendTo("#tmp_cat_dd_option");
		$("#tmp_cat_dd_option").attr("id", "edit-item-cat_id");
		$("#edit-item-cat_id option[id="+cat_id+"]").attr("selected", "selected");

		$(this).attr("id", "");

		$('td', parent_tr).each(function() {
			local_html = $(this).html();

			if (count == 1)
				$(this).html("<input id='edit-item-day' type=text size='5' value='" + local_html.substring( 0, local_html.length - 2 ) + "' />");
			else if (count == 2)
				$(this).html("<input id='edit-item-amount' type=text size='10' value='" + local_html + "' />");
			else if (count == 3)
				$(this).html("<input id='edit-item-comment' type=text size='50' value='" + local_html + "' />");
			else if (count == 4)
				$(this).html("<input id='edit-item-save' item-id='" + item_id + "' type=button value='Save' />");
			else if (count == 5)
				$(this).html("<input id='edit-item-cancel' cat-id='" + cat_id + "' type=button value='Cancel' />");
			else
				$(this).html();

			count++;
		});
	});

	$("#edit-item-cancel").live("click", function() {
		var cat_id = $(this).attr("cat-id");
		$.ajax({
			type: "POST",
			url: "ajax/income_details.php",
			data: {"cat_id":cat_id},
			success: function(data){
				$(".details-expand").html(data);
				$("#quick-type option[id='"+cat_id+"']").prop("selected", true);
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#edit-item-save").live("click", function() {
		var item_id 	= $(this).attr("item-id");
		var count 		= 1;
		var day			= $("#edit-item-day").val();
		var amount		= $("#edit-item-amount").val();
		var comment		= $("#edit-item-comment").val();
		var cat_id		= $("#edit-item-cat_id option:selected").attr("id");

		$.ajax({
			type: "POST",
			url: "ajax/update_item.php",
			data: {"item_id":item_id, "day":day, "amount":amount, "comment":comment, "cat_id":cat_id},
			success: function(data){
				if (data.error) {
					alert(data.message);
				} else {
					$.ajax({
						type: "POST",
						url: "index.php",
						data: "",
						success: function(data){
							$("body").html(data);
							$.ajax({
								type: "POST",
								url: "ajax/income_details.php",
								data: {"cat_id":cat_id},
								success: function(data){
									$(".details-expand").html(data);
									$("#quick-type option[id='"+cat_id+"']").prop("selected", true);
								},
								error: function(){
									alert("error");
								}
							});
						}
					});
				}
			},
			error: function(){
				alert("error");
			}
		});
	});

	$("#delete-item").live("click", function() {
		var parent 	= $(this).parent("td");
		var cat_id 	= parent.parents("table").attr("cat-id");

		if (confirm("Are you sure you want to delete this item?")) {
			$("#delete-item").die("click");

			$.ajax({
				type: "POST",
				url: "ajax/delete_item.php",
				timeout: 1000,
				data: {"item_id":$(this).attr("item-id")},
				success: function(data){
					$.ajax({
						type: "POST",
						url: "index.php",
						data: "",
						success: function(data){
							$("body").html(data);
							$.ajax({
								type: "POST",
								url: "ajax/income_details.php",
								data: {"cat_id":cat_id},
								success: function(data){
									$(".details-expand").html(data);
									$("#quick-type option[id='"+cat_id+"']").prop("selected", true);
								},
								error: function(){
									alert("error");
								}
							});
						}
					});
				}
			});
		}
	});

	$("#add-cat-submit").live("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/manage_cats.php",
			data: {	"cat_type":$("#cat-type option:selected").attr("id"),
					"cat_name":$("#add-cat-name").val(),
					"cat_budget":$("#add-cat-budget").val(),
					"cat_s_month":$("#add-start-month option:selected").val(),
					"cat_s_year":$("#add-start-year option:selected").val(),
					"cat_e_month":$("#add-end-month option:selected").val(),
					"cat_e_year":$("#add-end-year option:selected").val()},
			success: function(data){
				$("#content").html(data);
			}
		});
	});

	$("#edit-cat-save").live("click", function() {
		$.ajax({
			type: "POST",
			url: "ajax/cat_edit.php",
			data: {	"cat_id":$("#edit-id").val(),
					"edit_type":$("#edit-type option:selected").attr("id"),
					"edit_name":$("#edit-name").val(),
					"edit_budget":$("#edit-budget").val(),
					"edit_s_month":$("#edit-start-month option:selected").val(),
					"edit_s_year":$("#edit-start-year option:selected").val(),
					"edit_e_month":$("#edit-end-month option:selected").val(),
					"edit_e_year":$("#edit-end-year option:selected").val()
				  },
			success: function(data){
				$("#category-edit").html(data);
			}, error: function(response){
				alert("WTF date");
			}
		});
	});

	$("#delete-cat").live("click", function() {
		var con = confirm("Are you sure you want to delete this category, it will remove all items associated with it?");
		if (con) {
			$.ajax({
				type: "POST",
				url: "ajax/manage_cats.php",
				data: {"cat_id":$("#edit-id").val(), "delete":1},
				success: function(data){
					$("#content").html(data);
				}
			});
		}
	});
});