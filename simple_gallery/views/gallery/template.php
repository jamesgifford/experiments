<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />

<title>CSSDB.DEV</title>

<link rel="stylesheet" type="text/css" href="lib/farbtastic/farbtastic.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" language="javascript" src="lib/farbtastic/farbtastic.js"></script>
<script type="text/javascript" language="javascript" src="js/scripts.js"></script>
</head>

<body>

<!-- Gallery -->
<div id="gallery">
	<ul>
	<?php foreach ($designs as $index => $design) : ?>
		<li>
			<img src="/content/screenshots/<?= $design['screenshot'] ?>" title="<?= $design['title'] ?>" alt="<?= $design['title'] ?>" />
		</li>
	<?php endforeach; ?>
	</ul>
</div> <!-- End Gallery -->

<div id="controls">
	<a id="controls_toggle" title="Toggle controls menu" href="#">+</a>
	
	<ul id="controls_menu">
		<li><a id="tab_filters" title="Filters" href="#">Filters</a></li>
		<li><a id="tab_color" title="Colors" href="#">Color</a></li>
		<li><a id="tab_tags" title="Tags" href="#">Tags</a></li>
		<li><a id="tab_search" title="Search" href="#">Search</a></li>
		<li><a id="tab_options" title="Options" href="#">Options</a></li>
	</ul>
	
	<div class="control_panel" id="panel_filters">
		<a class="panel_tab" title="Info about the filters panel" href="#">Filters</a>
		<a class="panel_reset" title="" href="#">Reset</a>
		<div class="content">
			<p class="current_filters">
				<ul id="current_filters">
					<li class="empty">No filters</li>
				</ul>
			</p>
		</div>
	</div>
	
	<div class="control_panel" id="panel_color">
		<a class="panel_tab" title="Info about the colors panel" href="#">Colors</a>
		<a class="panel_reset" title="" href="#">Reset</a>
		<div class="content">
			<div id="color_picker"></div>
			<p class="current_color">
				<label for="current_color">Current color:</label>
				<input type="text" id="current_color" name="current_color" value="None selected" readonly="readonly" />
			</p>
		</div>
	</div>
	
	<div class="control_panel" id="panel_tags">
		<a class="panel_tab" title="Info about the tags panel" href="#">Tags</a>
		<a class="panel_reset" title="" href="#">Reset</a>
		<div class="content">
			<p class="tag_lists">
				<ul id="tag_lists">
				<?php $current_category = ''; foreach ($tags_with_category as $tag) : ?>
				
				<?php if ($current_category != '' && $current_category != $tag['category_name']) : ?>
							<div class="clear"></div>
						</ul>
					</li>
				<?php endif; ?>
				
				<?php if ($current_category == '' || $current_category != $tag['category_name']) : $current_category = $tag['category_name']; ?>
					<li class="tag_category">
						<h3><a href="#"><?= $tag['category_title'] ?></a></h3>
						<ul>
				<?php endif; ?>		
							<li><input type="checkbox" class="filter_tag" id="filter_tag_<?= $tag['tag_id'] ?>" name="<?= $tag['category_title'] ?>[]" value="<?= $tag['tag_name'] ?>" /><span><?= $tag['tag_title'] ?></span></li>
				<?php endforeach; ?>
							<div class="clear"></div>
						</ul>
					</li>
				</ul>
			</p>
			
			<p class="tag_cloud">
				<ol id="tag_cloud">
				<?php foreach ($tags_with_count as $tag) : ?>
					<li class="<?= $tag['popularity'] ?>"><span><?= $tag['count'] ?> designs are tagged with </span><a class="cloud_tag" id="cloud_tag_<?= $tag['tag_id'] ?>" href="#" title="<?= $tag['count'] ?> designs tagged with '<?= $tag['tag_title'] ?>'"><?= $tag['tag_title'] ?></a></li>
				<?php endforeach; ?>
				</ol>
			</p>
		</div>
	</div>
	
	<div class="control_panel" id="panel_search">
		<a class="panel_tab" title="Info about the search panel" href="#">Search</a>
		<a class="panel_reset" title="" href="#">Reset</a>
		<div class="content">
			<p class="search_text">
				<input type="text" id="search_text" name="search" value="" />
			</p>
			<p class="search_fields">
				<label for="search_fields">Search in:</label>
				<input type="checkbox" name="search_fields[]" value="title" checked="checked" /><span>Title</span><br />
				<input type="checkbox" name="search_fields[]" value="description" checked="checked" /><span>Description</span><br />
				<input type="checkbox" name="search_fields[]" value="url" checked="checked" /><span>Url</span><br />
			</p>
			<p class="search_match">
				<label for="search_for">Match:</label>
				<input type="radio" name="search_match" value="any" checked="checked" /><span>Any word</span><br />
				<input type="radio" name="search_match" value="all" /><span>All words</span><br />
				<input type="radio" name="search_match" value="exact" /><span>Exact match</span><br />
			</p>
		</div>
	</div>
	
	<div class="control_panel" id="panel_options">
		<a class="panel_tab" title="Info about the options panel" href="#">Options</a>
		<a class="panel_reset" title="" href="#">Reset</a>
		<div class="content">
			<p class="sort_by">
				<label for="sort_by">Sort by</label>
				<select id="sort_by">
					<option value="added_date">Date Added</option>
					<option value="title">Title</option>
				</select>
				<select id="sort_direction">
					<option value="asc">Ascending</option>
					<option value="desc">Descending</option>
				</select>
			</p>
		</div>
	</div>
	
	<input type="hidden" id="controls_state" />
	<input type="hidden" id="current_control" />
</div>

</body>
</html>