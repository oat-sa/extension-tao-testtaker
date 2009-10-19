<?if(get_data('message')):?>
<div class="ui-state-highlight ui-corner-all" style="width:80%; text-align:center; margin: auto;">
	<span style="font-style:italic;color:#D2184B;"><?=__(get_data('message'))?></span>
</div>
<br />
<?endif?>

<div style="width:100%;position:relative; ">
	<div style="position:absolute; width:30%; left:0%; top:0%;">
		<div id="accordion" class="ui-accordion ui-widget ui-helper-reset">
			<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all">
			    <span class="ui-icon"/>
			     <a href="#">default subjects models</a>
			  </h3>
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="padding:0em 0em 1em 1em;">
				<div id="common-subject" ></div>
			</div>
			
			<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all">
			    <span class="ui-icon"/>
			     <a href="#">custom subjects models</a>
			  </h3>
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="padding:0em 0em 1em 1em;">
				<div id="custom-subject" ></div>				
			</div>
		</div>
		<div style="margin:15px;">
			<span class="ui-state-default ui-corner-all" style="padding:5px;margin-right:10px;">
				<img src="<?=BASE_WWW?>img/add.png" />
				<a class='form-nav' href="<?=_url('addModel')?>" >Add a Subject Model</a>
			</span>
		</div>
	</div>
	<div style="position:absolute;  width:68%; left:32%; top:0%; ">
		<div id="form-container" ></div>
	</div>
</div>
<script type="text/javascript">
	
	var treeOptions = {
		formContainer: '#form-container',
		editClassAction: "<?=_url('editModel')?>",
		editInstanceAction: "<?=_url('editModelInstance')?>", 
		classEditable: true,
		createInstanceAction: "<?=_url('createInstance')?>"
	};
	
	var activeItem = <?=get_data('currentNode')?>;
	$(function(){
		
		$(".ui-accordion").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: true,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		
		new GenerisTreeClass('#common-subject', "<?=_url('getSubjectModel')?>", treeOptions);
		new GenerisTreeClass('#custom-subject', "<?=_url('getSubjectModel')?>", treeOptions);
		
	});
	
</script>
<?include('footer.tpl');?>