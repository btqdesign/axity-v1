<?php
if ( ! function_exists( 'ampforwp_minimalblog_theme_settings' ) ) {
	function ampforwp_minimalblog_theme_settings($sections){
                
		$sections[] = array(
		      'title'      => __( 'Minimal Blogging Theme', 'ampminimalblogtheme' ),
		      'icon'       => 'el el-idea',
			    'id'         => 'ampforwp-minimalblog-theme-subsection',
		      'desc'       => " ",
							);
        $sections[] = array(
              'title'      => __( 'Settings', 'ampminimalblogtheme' ),
              'desc'       => __( '', 'ampminimalblogtheme'),
              'id'         => 'ampforwp-minimalblog-theme-settings',
              'subsection' => true,
              'fields'      => array(
                   
                 //Color Scheme section
                array(
                      
                       'id'     => 'global-color-scheme',
                       'type'   => 'section',
                       'title'  => __('Color Scheme', 'ampminimalblogtheme'),
                       'indent' => true,
                ),
                array(
                        'id'        => 'minimalblog-color-scheme',
                        'title'     => __('Global Color Scheme', 'ampminimalblogtheme'),
                        'subtitle'  => __('Choose the color for title, anchor link','ampminimalblogtheme'),
                        'type'      => 'color_rgba',
                        'default'   => array(
                        'rgba'      => '(255,61,37,1)',
                         ),
                ),

                //Single Options
                array(
                       'id'     => 'minimalblog-single',
                       'type'   => 'section',
                       'title'  => __('Single', 'ampminimalblogtheme'),
                       'indent' => true,
                ),
                 array(
                        'id'    => 'minimalblog-date',
                        'type'  => 'switch',
                        'title' => __('Date', 'ampminimalblogtheme'),
                        'subtitle'  => __('Enable to show Post Date', 'ampminimalblogtheme'),
                        'default'   => 1
                ),
                 array(
                        'id'    => 'minimalblog-author-box',
                        'type'  => 'switch',
                        'title' => __('Author Box', 'ampminimalblogtheme'),
                        'subtitle'  => __('Switch to show/hide author box', 'ampminimalblogtheme'),
                        'default'   => 1
                ),
                 array(
                        'id'    => 'minimalblog-social-icons',
                        'type'  => 'switch',
                        'title' => __('Social Icons', 'ampminimalblogtheme'),
                        'subtitle'  => __('Switch to show/hide Social Icons', 'ampminimalblogtheme'),
                        'default'   => 1
                ),
                array(
                        'id'    => 'minimalblog-comment',
                        'type'  => 'switch',
                        'title' => __('Comments', 'ampminimalblogtheme'),
                        'subtitle'  => __('Switch to show/hide Comments', 'ampminimalblogtheme'),
                        'default'   => 1
                ),
                   array(
                        'id'    => 'minimalblog-taxonomy',
                        'type'  => 'switch',
                        'title' => __('Taxonomy Tags', 'ampminimalblogtheme'),
                        'subtitle'  => __('switch to show/hide taxonomy tags', 'ampminimalblogtheme'),
                        'default'   => 1
                ),

              //Footer Options
                array(
                       'id'     => 'minimalblog-single',
                       'type'   => 'section',
                       'title'  => __('Footer', 'ampminimalblogtheme'),
                       'indent' => true,
                ),
                 array(
                        'id'    => 'minimalblog-menu',
                        'type'  => 'switch',
                        'title' => __('Menu', 'ampminimalblogtheme'),
                        'subtitle'  => __('switch to show/hide Menu', 'ampminimalblogtheme'),
                        'default'   => 1
                ),

            ),
          );

        return $sections;
    }
}
add_filter("redux/options/redux_builder_amp/sections", 'ampforwp_minimalblog_theme_settings');