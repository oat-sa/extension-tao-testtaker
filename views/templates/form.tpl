<?if(get_data('message')):?>
<div class="ui-state-highlight ui-corner-all" style="padding:5px;margin-right:10px;text-align:center;">
	<?=get_data('message')?>
</div>
<br />
<?endif?>

<div class="ui-widget-header ui-corner-top ui-state-default" style="padding:5px;"><?=get_data('formTitle')?>:</div>
<div class="ui-widget-content ui-corner-bottom ui-state-default" style="padding:5px; font-size:0.9em;">
	<?=get_data('myForm')?>
</div>
<?include('footer.tpl');?>
