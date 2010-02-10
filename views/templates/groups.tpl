<div id="group-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Add to group')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="group-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-group" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	new GenerisTreeFormClass('#group-tree', "/taoSubjects/Subjects/getGroups", {
		actionId: 'group',
		saveUrl : '/taoSubjects/Subjects/saveGroups',
		checkedNodes : <?=get_data('subjectGroups')?>
	});
});
</script>