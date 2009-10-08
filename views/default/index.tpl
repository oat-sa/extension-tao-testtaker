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
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
				<table class="tree">
				<?foreach(get_data('commonModels') as $id => $data):?>
					<tr id="node-<?=$id?>">
						<td>
							<a id="item-<?=$id?>" class='item' href="#" ><?=$data['label']?></a>
							<ul id="menu-<?=$id?>" class="menu" style="display:none;">
								<li><a class='form-nav' href="<?=_url('type=0&uri='.$data['uri'], 'addModelInstance')?>">add instance</a></li>
								<li><a class='form-nav' href="<?=_url('type=0&uri='.$data['uri'], 'editModel')?>">edit</a></li>
								<li><a class='nav' href="<?=_url('type=0&uri='.$data['uri'], 'deleteModel')?>">delete</a></li>
							</ul>
						</td>
					</tr>
					<?foreach($data['instances'] as $iid => $idata):?>
					<tr class="child-of-node-<?=$id?>">
						<td>
							<a id="item-<?=$iid?>" class='item' href="#" ><?=$idata['label']?></a>
							<ul id="menu-<?=$iid?>" class="menu" style="display:none;">
								<li>
									<a class='form-nav' href="<?=_url(array('type'=> 1, 'uri'=> $idata['uri'], 'classUri' => $data['uri']), 'editModelInstance')?>">edit</a>
								</li>
								<li>
									<a class='nav' href="<?=_url(array('type'=> 1, 'uri'=> $idata['uri'], 'classUri' => $data['uri']), 'deleteModelInstance')?>">delete</a>
								</li>
							</ul>
						</td>
					</tr>
					<?endforeach?>
				<?endforeach?>
				</table>
			</div>
			
			<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all">
			    <span class="ui-icon"/>
			     <a href="#">custom models</a>
			  </h3>
			<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
				<table class="tree">
				<?foreach(get_data('customModels') as $id => $data):?>
					<tr id="node-<?=$id?>">
						<td>
							<a id="item-<?=$id?>" class='item' href="#" ><?=$data['label']?></a>
							<ul id="menu-<?=$id?>" class="menu" style="display:none;">
								<li><a class='nav' href="<?=_url('type=1&uri='.$data['uri'], 'addModelInstance')?>">add instance</a></li>
								<li><a class='nav' href="<?=_url('type=1&uri='.$data['uri'], 'editModel')?>">edit</a></li>
								<li><a class='nav' href="<?=_url('type=1&uri='.$data['uri'], 'deleteModel')?>">delete</a></li>
							</ul>
						</td>
					</tr>
					<?foreach($data['instances'] as $iid => $idata):?>
					<tr class="child-of-node-<?=$id?>">
						<td>
							<a id="item-<?=$iid?>" class='item' href="#" ><?=$idata['label']?></a>
							<ul id="menu-<?=$iid?>" class="menu" style="display:none;">
								<li>
									<a class='nav' href="<?=_url(array('type'=> 1, 'uri'=> $idata['uri'], 'classUri' => $data['uri']), 'editModelInstance')?>">edit</a>
								</li>
								<li>
									<a class='nav' href="<?=_url(array('type'=> 1, 'uri'=> $idata['uri'], 'classUri' => $data['uri']), 'deleteModelInstance')?>">delete</a>
								</li>
							</ul>
						</td>
					</tr>
					<?endforeach?>
				<?endforeach?>
				</table>
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
</script>
<script type="text/javascript" src="<?=BASE_WWW?>js/tree.js"></script>
<?include('footer.tpl');?>