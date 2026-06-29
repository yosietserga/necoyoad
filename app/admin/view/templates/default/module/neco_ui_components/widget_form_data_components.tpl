<div class="row">

	<div class="searchBoxComponents">
		<input type="text" name="searchComponents" value="" placeholder="Filtrar Componentes..." onkeydown="filterComponents( this.value );" />
	</div>

	<?php 
	$components = array(
		array(
			'id'=>4,
			'name'=>'separator',
			'title'=>'Separator',
			'description'=>'Something to say',
			'icon'=>'fa-home'
		),
		array(
			'id'=>4,
			'name'=>'title',
			'title'=>'Title',
			'description'=>'Something to say',
			'icon'=>'fa-home'
		),
		array(
			'id'=>4,
			'name'=>'tabs',
			'title'=>'Tabs',
			'description'=>'Something to say',
			'icon'=>'fa-home'
		),
		array(
			'id'=>4,
			'name'=>'photo',
			'title'=>'Photo',
			'description'=>'Something to say',
			'icon'=>'fa-home'
		),
	); 
	?>
	<ul class="listComponents">
		<?php foreach ($components  as $v) { ?>
		<li data-component="<?php echo $v['name']; ?>">
			<i class="fa <?php echo $v['icon']; ?>"></i>
			<?php echo $v['title']; ?>
			<small><?php echo $v['description']; ?></small>
		</li>
		<?php } ?>
	</ul>
</div>


<script>

function filterComponents( filter ) {
	if (filter.length > 0) {
		$('.listComponents li').each(function(){
			let name = $(this).data('component');

			if (name.indexOf( filter ) != -1) {
				$(this).removeClass('hidden');
			} else {
				$(this).addClass('hidden');
			}
		});
	} else {
		$('.listComponents li').removeClass('hidden');
	}
}
</script>