$(function(){

	//models: 1st tree level rendered in an accordion  
	$(".ui-accordion").accordion({
		fillSpace: false,
		autoHeight: false,
		collapsible: true,
		active: 0,
		icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
	});
	
	//render the html table into an expandable tree
	/*$(".tree").treeTable({
		clickableNodeNames: false
	});
	
	$(".item").click(function(){
		item = $("#" + this.id.replace('item-', 'menu-'));
		if(item.css('display') == 'none'){
			item.css({'display': 'block'});
		}
		else{
			item.css({'display': 'none'});
		}
	});*/

});