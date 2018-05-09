</div>
<footer class="footer">
	<div class="cntr">
		<?php 
			global $redux_builder_amp;
			if(isset($redux_builder_amp['minimalblog-menu']) && $redux_builder_amp['minimalblog-menu']==1){?>    
		<div class="menu">
			<?php amp_sidebar(['action'=>'open-button']); ?>
		</div>
		<?php }?>
		<?php amp_social(); ?>
		<div class="rts"> 
			<?php amp_non_amp_link(); ?>
		</div>
	</div><!-- /.container -->

</footer>
<?php amp_sidebar(['action'=>'start',
    'id'=>'sidebar',
    'layout'=>'nodisplay',
    'side'=>'right'
] ); ?>
<div class="amp-close-btn">
    <?php amp_sidebar(['action'=>'close-button']); ?>
</div>
<?php amp_menu(); ?>
<?php amp_search();?>
<?php amp_sidebar(['action'=>'end']); ?>
