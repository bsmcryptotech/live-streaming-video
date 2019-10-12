<div id="modal_edit_ad_<?php echo $ad['id'];?>" class="modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_edit_ad_<?php echo $ad['id'];?>">Edit Ad</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form name="preroll_update_<?php echo $ad['id'];?>" method="post" action="player-static-ads.php?act=edit&id=<?php echo $ad['id'];?>">
				<div class="modal-body" style="max-height: 400px; overflow-y:scroll;">
					<div id="preroll_update_<?php echo $ad['id'];?>">
						<div class="preroll_update_form">
						<div class="form-group">
							<label>Name</label>
							<input type="text" name="name" value="<?php echo htmlspecialchars($ad['name']);?>" size="40" class="form-control" />
						</div>
						<div class="form-group">
							<label>Duration</label>
							<div class="input-group">					
								<input type="text" name="duration" id="appendedInput" value="<?php echo $ad['duration'];?>" autocomplete="off" size="25" class="form-control col-md-3" />
								<div class="input-group-append">
									<span class="input-group-text border-left-0 bg-transparent">seconds</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>HTML Code for your Ad</label>
							<textarea name="code" cols="60" rows="4" class="form-control"><?php echo $ad['code'];?></textarea>
						</div>
						<div class="form-group">
							<label>Display to</label>
							<select name="user_group" class="custom-select">
								<option value="0" <?php echo ($ad['user_group'] == 0) ? 'selected="selected"' : '';?>>All visitors</option>
								<option value="1" <?php echo ($ad['user_group'] == 1) ? 'selected="selected"' : '';?>>Logged-in users only</option>
								<option value="2" <?php echo ($ad['user_group'] == 2) ? 'selected="selected"' : '';?>>Visitors only</option>
							</select>
						</div>
						<div class="form-group">
							<label>Allow viewers to 'Skip' this Ad</label>
							<br />
							<label class="m-0 mr-2"><input type="radio" name="skip_delay_radio" value="1" <?php echo ($ad['skip'] == 1) ? 'checked="checked"' : '';?> child-input="skip_delay_seconds_span_<?php echo $ad['id'];?>"> Yes</label>
							<label class="m-0 mr-2"><input type="radio" name="skip_delay_radio" value="0" <?php echo ( ! $ad['skip']) ? 'checked="checked"' : '';?> child-input="skip_delay_seconds_span_<?php echo $ad['id'];?>"> No</label>
						</div>
						<div class="form-group">
							<span id="skip_delay_seconds_span_<?php echo $ad['id'];?>" <?php echo ($ad['skip'] == 0) ? 'class="hide"' : '';?>>
								<label>Display 'Skip' option after</label>
								<div class="input-group-append">
								<input type="text" name="skip_delay_seconds" id="appendedInput" value="<?php echo (int) $ad['skip_delay_seconds'];?>" class="form-control" />
								<span class="input-group-text border-left-0 bg-transparent">seconds</span>
								</div>
							</span>
						</div>
						<div class="form-group">
							<label>Don't display on videos in</label>
							<?php 
								$categories_dropdown_options = array(
																'attr_name' => 'ignore_category[]',
																'attr_id' => 'main_select_category_'. $ad['id'],
																'attr_class' => 'category_dropdown',
																'select_all_option' => false,
																'spacer' => '&mdash;',
																'selected' => (array) $ad['ignore_category'],
																'other_attr' => 'multiple="multiple" data-placeholder="Select categories..."'
																);
								echo categories_dropdown($categories_dropdown_options);
							?>
						</div>
						<div class="form-group">
							<label>Don't display on videos from</label>
							<select name="ignore_source[]" data-placeholder="Select sources..." id="main_select_sources_<?php echo $ad['id'];?>" class="source_dropdown" multiple="multiple">
							<?php

								foreach ($sources as $id => $src)
								{
									$selected = (is_array($ad['ignore_source']) && in_array($id, $ad['ignore_source'])) ? 'selected="selected"' : '';
									$option = '';
									if (is_int($id) && $id > 1 && $id != 44 && $id != 43): ?>
										<option value="<?php echo $src['source_id'];?>" <?php echo $selected;?>><?php echo ucfirst($src['source_name']);?></option>
									<?php 
									endif;
								}
							?>
							</select>
						</div>
						<div class="form-group">
							<label>Enable Statistics</label>
							<br />
							<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="0" <?php echo ($ad['disable_stats'] == 0) ? 'checked="checked"' : '';?>> Yes</label> 
							<label class="m-0 mr-2"><input type="radio" name="disable_stats" value="1" <?php echo ($ad['disable_stats'] == 1) ? 'checked="checked"' : '';?>> No</label>
						</div>

							</div>
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