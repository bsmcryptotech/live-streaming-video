<!-- create new ad form modal -->
<div id="addNew" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Create new pre-roll ad</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form name="ad_manager" method="post" action="player-static-ads.php?act=addnew">
			<div class="modal-body" style="max-height: 400px; overflow-y:scroll">
				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" value="" size="40" class="form-control" />
				</div>
				<div class="form-group">
					<label>Duration</label>
					<div class="input-group">					
						<input type="text" name="duration" id="appendedInput" value="30" autocomplete="off" size="25" class="form-control col-md-3" />
						<div class="input-group-append">
							<span class="input-group-text border-left-0 bg-transparent">seconds</span>
						</div>
					</div>
				</div>	

				<div class="form-group">
					<label>HTML Code for your Ad</label>
					<textarea name="code" cols="60" rows="4" class="form-control"></textarea>
				</div>

				<div class="form-group">
					<label>Display to</label>
					<select name="user_group" class="custom-select">
						<option value="0">All visitors</option>
						<option value="1">Logged-in users only</option>
						<option value="2">Visitors only</option>
					</select>
				</div>
					<hr />
				<div class="form-group">
					<label>Allow viewers to 'Skip' this Ad</label> 
					<br />
					<label class="m-0 mr-2"><input type="radio" name="skip_delay_radio" value="1"> Yes</label> 
					<label class="m-0 mr-2"><input type="radio" name="skip_delay_radio" value="0" checked="checked"> No</label>
				</div>
					<hr />

				<div class="form-group">
					<span id="skip_delay_seconds_new_span">
						<label>Display 'Skip' option after</label>
						<div class="input-group-append">
						<input type="text" name="skip_delay_seconds" id="appendedInput" value="5" class="form-control" />
						<span class="input-group-text border-left-0 bg-transparent">seconds</span>
						</div>
					</span> 
				</div>
				<div class="form-group">
					<label>Don't display on videos in</label>
					<?php 
						$categories_dropdown_options = array(
														'attr_name' => 'ignore_category[]',
														'attr_id' => 'main_select_category',
														'attr_class' => 'category_dropdown custom-select',
														'select_all_option' => false,
														'spacer' => '&mdash;',
														'selected' => false,
														'other_attr' => 'multiple="multiple" data-placeholder="Select categories..."'
														);
						echo categories_dropdown($categories_dropdown_options);
					?>
				</div>
				<div class="form-group">
					<label>Don't display on videos from</label>
					<select name="ignore_source[]" data-placeholder="Select sources..." id="main_select_sources" multiple="multiple" class="source_dropdown category_dropdown">
					<?php
						foreach ($sources as $id => $src)
						{
							$option = '';
							if (is_int($id) && $id > 1 && $id != 44 && $id != 43): ?>
								<option value="<?php echo $src['source_id'];?>"><?php echo ucfirst($src['source_name']);?></option>
							<?php 
							endif;
						}
					?>
					</select>
				</div>

				<div class="form-group">
					<label>Enable Statistics</label>
					<br />
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0"> Yes</label>
					<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" checked="checked"> No</label>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="status" value="1" />
				<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
				<button type="submit" name="Submit" value="Submit" class="btn btn-success" />Save</button>
			</div>
			</form>
		</div>
	</div>
</div>