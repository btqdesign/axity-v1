<?php while(amp_loop('start')): ?>
	<?php $args = array("tag"=>'div',"tag_class"=>'image-container','image_size'=>'large', 'responsive'=> false); ?>
	<div class="lp-list">
		<div class="feat-img">
			<?php amp_loop_image($args); ?>
		</div>
		<div class="feat-lay">
			<div class="lp">
			    <div class="cntr">
				    <?php amp_loop_category(); ?>
				    <?php amp_loop_title(); ?>
				    <?php amp_loop_excerpt(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php endwhile; amp_loop('end');  ?>
	<div class="cntr">
		<?php amp_pagination(); ?>
	</div>
	