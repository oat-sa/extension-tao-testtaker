<script type="text/javascript">
$(function(){

	<?if(get_data('reload') === true):?>	
		
		<?if(get_data('showNodeUri')):?>
			var showNode = "<?=get_data('showNodeUri')?>";
		<?else:?>
			var showNode = false;
		<?endif?>
		
	loadControls();
	
	<?else:?>
	
	initNavigation();
	
	<?endif?>
});
</script>