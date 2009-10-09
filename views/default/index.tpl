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
			     <a href="#">default models</a>
			  </h3>
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="padding:0em 0em 1em 1em;">
				<div id="common-subject-tree" ></div>
			</div>
			
			<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all">
			    <span class="ui-icon"/>
			     <a href="#">custom models</a>
			  </h3>
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="padding:0em 0em 1em 1em;">
				<div id="custom-subject-tree" ></div>				
			</div>
		</div>
		<div style="margin:15px;">
			<span class="ui-state-default ui-corner-all" style="padding:5px;margin-right:10px;">
				<img src="<?=BASE_WWW?>img/add.png" />
				<a class='form-nav' href="<?=_url(null, 'addModel')?>" >Add a Subject Model</a>
			</span>
		</div>
	</div>
	<div style="position:absolute;  width:68%; left:32%; top:0%; ">
		<div id="form-container" ></div>
	</div>
</div>
<br />
<script type="text/javascript">
	var activeItem = <?=get_data('currentNode')?>;
	$(function(){
		$(".ui-accordion").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: true,
			active: 0,
			icons: { 'header': 'ui-icon-plus', 'headerSelected': 'ui-icon-minus' }
		});
		$("#common-subject-tree, #custom-subject-tree").tree({
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: "<?=_url(null, 'getSubjectModel')?>" 
				}
			},
			types: {
			 "default" : {
					renameable	: false,
					deletable	: true,
					creatable	: true,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback : {
				beforedata:function(NODE, TREE_OBJ) { 
					return { 
						modelType : $(TREE_OBJ.container).attr('id').replace('-subject-tree','') 
					} 
				},
				onselect: function(NODE, TREE_OBJ){
					try{
						if($(NODE).hasClass('node-class')){
							openForm("<?=_url(array('currentNode'=> 1), 'editModel')?>classUri="+$(NODE).attr('id'));
						}
						if($(NODE).hasClass('node-instance')){
							PNODE = TREE_OBJ.parent(NODE);
							openForm("<?=_url(array('currentNode'=> 1), 'editModelInstance')?>classUri="+$(PNODE).attr('id') + "&uri="+$(NODE).attr('id'));
						}
					}
					catch(exp){alert(exp)}
					return false;
				}
			},
			plugins: {
				contextmenu : {
					items : {
						edit: {
							label: "Edit",
							icon: "",
							visible : function (NODE, TREE_OBJ) {
								return true;
							},
							action  : function(NODE, TREE_OBJ){
								TREE_OBJ.select_branch(NODE);
							},
		                    separator_before : true
						},
						create:{
							label: "Create instance",
							visible: function (NODE, TREE_OBJ) {
								if(NODE.length != 1) {
									return false; 
								}
								if(!$(NODE).hasClass('node-class')){ 
									return false;
								}
								return TREE_OBJ.check("creatable", NODE);
							}
						},
						rename: false,
					}
				}
			}
		});
	});
</script>
<?include('footer.tpl');?>