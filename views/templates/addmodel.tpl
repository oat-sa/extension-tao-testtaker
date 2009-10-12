<div><strong><?=get_data('sent')?></strong></div>

<div class="ui-widget-header ui-corner-top ui-state-default" style="padding:5px;">Add a subject model:</div>
<div class="ui-widget-content ui-corner-bottom ui-state-default" style="padding:5px; font-size:0.9em;">

	<form method="post">
		<fieldset>
			<legend>Model Name</legend>
			<input type="text" />
		</fieldset>
		<fieldset>
			<legend>Property #1</legend>
			<table>
				<tbody>
					<tr>
						<td style="width:25%;">Name</td>
						<td colspan="2"><input type="text" /></td>
					</tr>
					<tr>
						<td>Type</td>
						<td style="background-color:#DDD;"><input type="radio" name="prop_type"> new</td>
						<td style="background-color:#BBB;"><input type="radio" name="prop_type" > existing</td>
					</tr>
					<tr>
						<td></td>
						<td style="background-color:#DDD;">
							<select>
								<option> - select - </option>
								<optgroup label="simple">
									<option>number</option>
									<option>short text</option>
									<option>long text</option>
									<option>date</option>
								</optgroup>
								<optgroup label="complex">
									<option>exclusif choice</option>
									<option>multiple choice</option>
								</optgroup>
						   </select>
						</td>
						<td style="background-color:#BBB;">
							<select>
								<option> - select - </option>
								<?foreach(get_data('commonProperties') as $uid => $data):?>
									<option value="<?=$uid?>"><?=$data['label']?></option>
								<?endforeach?>
						   </select>
						</td>
					</tr>
					<tr>
						<td>Required</td>
						<td colspan="2">
							<input type="radio" name="required" >Yes <input type="radio" name="required" >No
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<div style="padding:5px;border: solid 1px #cfcfcf;margin:10px 2px 5px 2px; ">
			<img src="<?=BASE_WWW?>img/add.png" />&nbsp;
			<a href="#" style="font-size:0.8em;">new property</a>
		</div>
		<br />
		<div style="text-align:center;">
			<input type="submit" value="Add" />
		</div>
	</form>
	<?//get_data('myForm');?>
</div>
<br />
<br />
