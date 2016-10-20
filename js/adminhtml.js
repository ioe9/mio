//widget
function removeRelateProduct(e,pid){
	var jsObjPre = jQuery(e).parent().parent().parent().attr("id").replace("label","");
	
	var newRelate = new Array();
	jQuery(e).parent().siblings().each(function(){
		newRelate.push(jQuery(this).attr("data"));
	})
	jQuery("#"+jsObjPre+"value").val("product/"+newRelate.join(","));
	jQuery(e).parent().remove();
}
