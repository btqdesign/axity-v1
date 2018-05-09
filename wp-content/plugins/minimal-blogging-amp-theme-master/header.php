<?php amp_header_core() ?>
 <header class="header">
    <div class="head-sec cntr">
        <div class="left">
            <?php amp_logo(); ?>
        </div>
        <div class="right">
            <?php amp_sidebar(['action'=>'open-button']); ?>         
        </div>

        <div class="clearfix"></div>
    </div><!-- /.header-section -->
</header>


<?php amp_sidebar(['action'=>'start',
    'id'=>'sidebar',
    'layout'=>'nodisplay',
    'side'=>'right'
] ); ?>
<div class="amp-close-btn">
    <?php amp_sidebar(['action'=>'close-button']); ?>
</div>
<div class="main-menu">
    <?php amp_menu(); ?>
    <?php amp_search();?>
</div>
<?php amp_sidebar(['action'=>'end']); ?>

<div class="content-wrapper">