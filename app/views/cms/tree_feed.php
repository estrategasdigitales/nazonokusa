<div id="tree-feed" class="tree"></div>
<input type="hidden" name="tree_json" value="<?php echo $nodes; ?>">
<script type="text/javascript">
	TelevisaFeed.Feed.tree(<?php echo $nodes ?>);
</script>