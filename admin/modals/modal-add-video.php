<div id="addVideo" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-semibold">Add Media</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<table cellpadding="0" cellspacing="0" width="100%" class="pm-add-tables">
					<tr>
						<td width="20%" align="right" class="text-left"><h6 class="mt-2 mb-1">Import Videos</h6></td>
						<td width="80%" align="left">
							<form name="search_yt_videos" action="import.php?action=search" method="post" class="">
								<div class="input-group mt-2 mb-1">
									<input name="keyword" type="text" value="" placeholder="Search for ..." class="form-control" id="yt_query" autocomplete="yt-keyword" /> 
									<div class="input-group-append">
										<select name="data_source" class="custom-select">
											<option value="youtube" selected="selected">Youtube</option>
											<option value="dailymotion">Dailymotion</option>
											<option value="vimeo">Vimeo</option>
										</select>

									</div>
									<div class="input-group-append">
										<button type="submit" name="submit" class="btn btn-primary" id="searchVideos" data-loading-text="Searching...">Search</button>
										<input type="hidden" name="autofilling" value="1" />
										<input type="hidden" name="autodata" value="1" />
										<input type="hidden" name="results" value="20"> 
									</div>
								</div>
							</form>
						</td>
					</tr>
					<tr>
						<td align="center" class="text-left"><h6 class=" mt-2 mb-1">Direct URL</h6></td>
						<td align="left">
						<form name="add" action="add-video.php?step=2" method="post" class="row-form">
							<div class="input-group mt-2 mb-1">
								<input type="text" id="addvideo_direct_input" class="form-control" name="url" placeholder="https://" /> 
								<div class="input-group-append">
									<input type="hidden" name="" value=""> 
									<button type="submit" id="addvideo_direct_submit" name="Submit" value="Step 2" class="btn btn-primary">Continue</button>
								</div>
							</div>
						</form>
						</td>
					</tr>
					<tr>
						<td align="center" class="text-left"><h6 class=" mt-1 mb-1">Upload</h6></td>
						<td align="left">
							<form name="upload-video-modal-form" id="upload-video-modal-form" enctype="multipart/form-data" action="admin-ajax.php" method="post" class="">
								<div class="input-group mt-2 mb-1">
									<div class="upload-file-dropzone" id="upload-video-modal-dropzone">
									<div class="fileinput-button">
										<input type="file" name="file" id="upload-video-modal-btn" class="file-input form-control form-control-sm btn-block w-100" data-show-caption="false" data-show-upload="false" data-browse-icon="<i class='icon-upload4 mr-2'></i>" data-browse-label="Select file" data-browse-class="btn btn-primary btn-sm font-weight-semibold btn-block" data-remove-class="btn btn-light btn-sm" data-show-remove="false" data-show-preview="false" />
									</div>
									</div>
									<input type="hidden" name="upload-type" value="" /> 
									<input type="hidden" name="p" value="upload" /> 
									<input type="hidden" name="do" value="upload-file" />
								</div>
							</form>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>