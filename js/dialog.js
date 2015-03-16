/*Dialog = (function (a){
	this.title = (typeof a == "undefined" || typeof a.title == "undefined") ? "Error" : a.title;
	this.message = (typeof a == "undefined" || typeof a.message == "undefined") ? "An error has occurred." : a.message;
	this.confirm = (typeof a == "undefined" || typeof a.confirm == "undefined") ? "Okay" : a.confirm;
	this.cancel = (typeof a == "undefined" || typeof a.cancel == "undefined") ? "" : a.cancel;
	this.onconfirm = function(){};
	this.oncancel = function(){};
});

Dialog.prototype.showDialog = (function(){
	var oncancelFunction = this.oncancel;
	var onconfirmFunction = this.onconfirm;
	var overlay = $("#overlay");
	var content = $(".pop_content");
	$("#dialog_title").text(this.title);
	$("#dialog_body").html(this.message);
	$("#dialog_confirm").val(this.confirm);
	$("#dialog_cancel").val(this.cancel).parent().attr("style",(this.cancel == "") ? "display:none;" : "");
	content.css({"top":-content.height() + "px", "left":(($(window).width() - content.width())/2)});
	$(document).unbind("keydown").one("keydown",function(event){
		if (event.which == 27){			
			content.animate({"top":-content.height()+"px"}, 500, function(){
				overlay.fadeOut(200);
			});
			$("#dialog_confirm,#dialog_cancel").unbind("click");
		}
	});

	$("#dialog_confirm,#dialog_cancel").unbind("click").one("click",function(){
		content.animate({"top":-content.height()+"px"}, 500, function(){
			overlay.fadeOut(200);
		});
		if ($(this).attr("id") == "dialog_confirm"){
			onconfirmFunction();
		}else{
			oncancelFunction();		
		}
	});
	
	overlay.css("height",($(document).height())+"px").fadeTo("fast",.3,function(){
		content.animate({"top":"160px"}, 500);
    });
});*/
