<div class="col-sm-12 col-md-6" id="stack-id-<?php echo str_replace(' ', '-', $counter); ?>">
	<div class="card video-stack<?php if ($_POST['checkall'] == 'true') echo ' stack-selected'; if ( ! $item['embeddable'] || $item['private']) echo ' stack-unusable';?>">
		<input type="hidden" name="stack_id[<?php echo $counter;?>]" value="stack-id-<?php echo str_replace(' ', '-', $counter); ?>" />
		<?php if ($item['has_errors'] && pm_count($item['errors']) > 0) : // CSV items ?>
		<div class="alert alert-danger bg-danger m-2 p-2">
			<ul class="list-unstyled">
			<?php foreach ($item['errors'] as $k => $error_msg) : ?>
				<li><?php echo $error_msg; ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		<div class="card-header">
			<div>
				<div class="on_off" data-popup="tooltip" data-original-title="Select to import">
					<label for="video_ids[<?php echo $counter;?>]">IMPORT</label>
					<input type="checkbox" id="import-<?php echo $counter;?>" name="video_ids[<?php echo $counter;?>]" value="<?php echo $item['id'] .'" '; if( ! $item['embeddable'] || $item['private']) { echo 'disabled="disabled" class="check_ignore"'; } elseif ($_POST['checkall'] == 'true') { echo ' checked="checked"'; } ?>" />
				</div>
			</div>
			<a id="video-id-[<?php echo $counter;?>]"></a>
			<input id="video-title[<?php echo $counter;?>]" name="video_title[<?php echo $counter;?>]" type="text" value="<?php echo htmlspecialchars($item['title']); ?>" size="20" class="form-control video-stack-title" rel="tooltip" title="Click to edit" />
			<div class="text-muted font-size-sm">Published: <?php echo date('d M, Y', $item['publish_date_timestamp']); ?></div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-4">
					<ul class="thumbs_ul_import">
						<li class="stack-thumb-selected stack-thumb">
							<?php if ( ! $item['embeddable'] || $item['private']) : ?>
							<h5>This owner of this video doesn't allow embedding.</h5>
							<?php endif; ?>
							<?php if ( ! is_array($item['geo-restriction'])) : ?>
							<!-- <span class="video-stack-geo d-sm-none d-md-block"><a href="#video-id-[<?php echo $counter;?>]" data-popup="tooltip" data-placement="right" data-original-title="This video is geo-restricted to certain countries."><img src="img/ico-geo-warn.png" /></a></span> -->
							<?php endif; ?>
							<!-- <span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" data-popup="tooltip" data-placement="right" data-original-title="The default thumbnail for this video."><i class="icon-checkbox-checked2 text-danger"></i></a></span> -->
							<span class="stack-video-duration badge badge-dark"><?php echo ($item['duration']) ? sec2hms($item['duration']) : ''; ?></span>
							<?php if ($item['embeddable']) : ?>
								<span class="stack-preview d-sm-none d-md-block"><a href="<?php echo $item['url']; ?>" title="<?php echo htmlspecialchars($item['title']); ?>" target="_blank"><div class="pm-sprite ico-playbutton"></div></a></span>
							<?php endif; ?>
							<img src="<?php echo $item['thumbs'][0]['medium']; ?>" alt="" width="154" height="117" border="0" name="video_thumbnail" videoid="<?php echo $item['id']; ?>" rowid="<?php echo $counter;?>" class="card-img img-fluid" />
						</li>
						<!-- removed extra thumbnails -->
					</ul>
					<label>
						<input type="checkbox" name="featured[<?php echo $counter;?>]" id="check_ignore" value="1" /> Mark as <span class="badge badge-primary">FEATURED</span>
					</label>
					<?php if ( ! $item['embeddable']) : ?>
					<?php endif; ?>
				</div><!-- .video-stack-left -->
				<div class="col-md-8">
					<div class="form-group mb-2">
						<label class="text-uppercase font-weight-semibold">CATEGORY <span class="text-danger font-weight-bold">*</span></label> 
						<div class="video-stack-cats">
						<?php
						$categories_dropdown_options = array(
									'attr_name' => 'category['. $counter .'][]',
									'attr_id' => 'select_category-'. $counter,
									'select_all_option' => false,
									'spacer' => '&mdash;',
									'selected' => $overwrite_category,
									'other_attr' => 'multiple="multiple" size="3"',
									'option_attr_id' => 'check_ignore'
									);
						echo categories_dropdown($categories_dropdown_options);
						?>
						</div>
					</div>
					<div class="form-group mb-2">
						<label class="text-uppercase font-weight-semibold">DESCRIPTION</label>
						<textarea name="description[<?php echo $counter;?>]" id="description[<?php echo $counter;?>]" rows="2" class="video-stack-desc form-control"><?php if($autodata) echo $item['description'];?></textarea>
					</div>
					<div class="form-group mb-2">
						<label class="control-label text-uppercase font-weight-semibold" for="tags">TAGS</label>
						<div class="tagsinput bootstrap-tagsinput">
							<input type="text" id="tags_addvideo_<?php echo $counter;?>" name="tags[<?php echo $counter;?>]" value="<?php if($autodata) echo $item['keywords'];?>" class="tags form-control tags-input" />
						</div>
					</div>

					<input type="hidden" id="thumb_url_<?php echo $counter;?>" name="thumb_url[<?php echo $counter;?>]" value="<?php echo $item['thumbs'][0][$config['download_thumb_res']]; ?>" />				
					<input type="hidden" name="duration[<?php echo $counter;?>]" value="<?php echo $item['duration']; ?>" />
					<input type="hidden" name="direct[<?php echo $counter;?>]" value="<?php echo $item['url']; ?>" />
					<input type="hidden" name="url_flv[<?php echo $counter;?>]" value="" />
					<?php if ($data_source == 'csv') : ?>
					<input type="hidden" name="csv_item_id[<?php echo $counter;?>]" value="<?php echo $item['item_id'];?>" />
					<input type="hidden" name="source_id[<?php echo $counter;?>]" value="<?php echo $item['source_id'];?>" />
					<?php endif; ?>
				</div> <!-- .video-stack-right -->
			</div>	
		</div>
	</div>
</div>