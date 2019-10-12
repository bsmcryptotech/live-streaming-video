<!-- quick category add modal -->
<div id="modal_add_category" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Create <?php echo ($category_type == 'genre') ? 'Genre' : 'Category'; ?></h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form name="new-category" method="post" action="">
				<div class="modal-body">
						<div class="modal-response-placeholder hide"></div>

						<div class="form-group">
						<label><?php echo ($category_type == 'genre') ? 'Genre' : 'Category'; ?> Name</label>
						<input name="name" type="text" value="<?php if($_POST['name'] != '') { echo $_POST['name']; } ?>" placeholder="<?php echo ($category_type == 'genre') ? 'Genre' : 'Category'; ?> Name" size="22" class="form-control" />
						</div>
						
						<div class="form-group">
						<label>URL Slug <a href="#" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores." class="text-grey-300"><i class="mi-info-outline" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."></i></a></label>
						<input name="tag" type="text" value="<?php if($_POST['tag'] != '') { echo $_POST['tag']; } ?>" placeholder="URL Slug" size="10" class="form-control" /> 
						</div>
						
						<div class="form-group">
						<label>Create in</label>
							<?php
							echo categories_dropdown($categories_dropdown_options);
							?>
						</div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" aria-hidden="true" class="btn btn-outline bg-grey text-grey-800 btn-icon">Cancel</button>
					<a href="edit-category.php?mode=add&type=<?php echo $category_type;?>" class="btn btn-light">Use the advanced form</a>
					<button name="submit" type="submit" value="Add category" class="btn btn-success">Add <?php echo ($category_type == 'genre') ? 'Genre' : 'Category'; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>