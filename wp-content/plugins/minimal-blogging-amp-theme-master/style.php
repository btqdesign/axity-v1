/**** 
* AMP Framework Reset
*****/
body{ font-family: 'Poppins'; font-size: 16px; line-height:1.4; }
ol, ul{ list-style-position: inside }
p, ol, ul, figure{ margin: 0 0 1em; padding: 0; }
a, a:active, a:visited{ 
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    color: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> color:rgba(255,61,37,1); <?php } ?>text-decoration: none }
a:hover, a:active, a:focus{}
pre{ white-space: pre-wrap;}
.left{float:left}
.right{float:right}
.hidden{ display:none }
.clearfix{ clear:both }
blockquote{ background: #f1f1f1; margin: 10px 0 20px 0; padding: 15px;}
blockquote p:last-child {margin-bottom: 0;}
.amp-wp-unknown-size img {object-fit: contain;}
.amp-wp-enforced-sizes{ max-width: 100% }

/**** 
* Global Styling
*****/

/* Image Alignment */
.alignright{float:right}
.alignleft{float:left}
.aligncenter{display:block;margin-left:auto;margin-right:auto}
amp-iframe { max-width: 100%; margin-bottom : 20px; }
/* Captions */
.wp-caption{padding:0}
.wp-caption-text{font-size:12px;line-height:1.5em;margin:0;padding:.66em 10px .75em;text-align:center}
/* AMP Media */
amp-iframe,amp-instagram,amp-vine,amp-youtube{margin:0 -16px 1.5em}
amp-carousel>amp-img>img{object-fit:contain}

/****
* Font Family
*****/
@font-face{font-family:'Libre Baskerville';font-style:normal;font-weight:400;src:local('LibreBaskerville Regular'),local('LibreBaskerville-Regular'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/LibreBaskerville-Regular.ttf')}
@font-face{font-family:'Libre Baskerville';font-style:italic;font-weight:400;src:local('LibreBaskerville Italic'),local('LibreBaskerville-Italic'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/LibreBaskerville-Italic.ttf')}
@font-face{font-family:'Libre Baskerville';font-style:normal;font-weight:700;src:local('LibreBaskerville Bold'),local('LibreBaskerville-Bold'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/LibreBaskerville-Bold.ttf')}
@font-face{font-family:Poppins;font-style:normal;font-weight:300;src:local('Poppins-Light'),local('Poppins-Light'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/Poppins-Light.ttf')}
@font-face{font-family:Poppins;font-style:normal;font-weight:400;src:local('Poppins Regular'),local('Poppins-Regular'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/Poppins-Regular.ttf')}
@font-face{font-family:Poppins;font-style:normal;font-weight:500;src:local('Poppins Medium'),local('Poppins-Medium'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/Poppins-Medium.ttf')}
@font-face{font-family:Poppins;font-style:normal;font-weight:600;src:local('Poppins SemiBold'),local('Poppins-SemiBold'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/Poppins-SemiBold.ttf')}
@font-face{font-family:Poppins;font-style:normal;font-weight:700;src:local('Poppins Bold'),local('Poppins-Bold'),url('<?php echo plugin_dir_url(__FILE__) ?>fonts/Poppins-Bold.ttf')}

/****
* Container
*****/
.cntr{max-width:600px;margin:0 auto;padding:0 10px}

/****
* AMP Sidebar
*****/
amp-sidebar{background:#333;width:100%;padding-left:20%;padding-right:10%;padding-bottom:20%}
.amp-sidebar-button{position:relative}
.amp-sidebar-toggle span{display:block;height:2px;margin-bottom:6px;width:32px;background:#fff}
.amp-sidebar-toggle span:nth-child(2){top:7px}
.amp-sidebar-toggle span:nth-child(3){top:14px}
.amp-sidebar-close{display:inline-block;padding:15px 10px;color:#333;float:right;cursor:pointer}
.amp-menu{list-style-type:none;margin:20% 0 0 0;padding:0}
.amp-menu li a{color:#fff;font-family:'Poppins'}
.amp-menu li:hover a{background:transparent none repeat scroll 0 0}
.amp-search-wrapper{padding:40px 0px}
.amp-search-wrapper #s{border:none;width:80%;background:transparent;border-bottom:1px solid #8a7e7e;padding:10px;color:#fff;font-size:15px}
#amp-search-submit{cursor:pointer;background:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU2Ljk2NiA1Ni45NjYiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU2Ljk2NiA1Ni45NjY7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTZweCIgaGVpZ2h0PSIxNnB4Ij4KPHBhdGggZD0iTTU1LjE0Niw1MS44ODdMNDEuNTg4LDM3Ljc4NmMzLjQ4Ni00LjE0NCw1LjM5Ni05LjM1OCw1LjM5Ni0xNC43ODZjMC0xMi42ODItMTAuMzE4LTIzLTIzLTIzcy0yMywxMC4zMTgtMjMsMjMgIHMxMC4zMTgsMjMsMjMsMjNjNC43NjEsMCw5LjI5OC0xLjQzNiwxMy4xNzctNC4xNjJsMTMuNjYxLDE0LjIwOGMwLjU3MSwwLjU5MywxLjMzOSwwLjkyLDIuMTYyLDAuOTIgIGMwLjc3OSwwLDEuNTE4LTAuMjk3LDIuMDc5LTAuODM3QzU2LjI1NSw1NC45ODIsNTYuMjkzLDUzLjA4LDU1LjE0Niw1MS44ODd6IE0yMy45ODQsNmM5LjM3NCwwLDE3LDcuNjI2LDE3LDE3cy03LjYyNiwxNy0xNywxNyAgcy0xNy03LjYyNi0xNy0xN1MxNC42MSw2LDIzLjk4NCw2eiIgZmlsbD0iI0ZGRkZGRiIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K);border:none;text-indent:-999px;background-size:18px;display:inline-block;width:20px;height:20px;background-repeat:no-repeat;position:relative;top:17px;padding:10px}

/****
* AMP Navigation Menu with Dropdown Support
*****/
.toggle-navigation ul{list-style-type:none;margin:0;padding:0;display:inline-block;width:100%}
.toggle-navigation ul li{font-size:13px;border-bottom:1px solid rgba(0,0,0,.11);padding:11px 0;width:25%;float:left;text-align:center;margin-top:6px}
.toggle-navigation ul ul{display:none}
.toggle-navigation ul li a{color:#eee;padding:15px}
.toggle-navigation{display:none;background:#444}

/**** 
* Header
*****/
.head-sec{padding:0 30px;max-width: 100%;}
.header{
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> background:rgba(255,61,37,1); <?php } ?> padding:20px 0;
}
.head-sec .left,.head-sec .right{line-height:0;display:block}
.header h1{font-size:1.5em}
.amp-phone,.amp-sidebar-button,.amp-social{display:inline-flex}
.amp-phone{top:4px}
.header .amp-social{margin:0 19px}
.amp-sidebar-button{top:6px}
.amp-menu li.menu-item-has-children>ul>li{padding-left:25px;font-size:12px}
.amp-sidebar-close:after{content:"";background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ3Ljk3MSA0Ny45NzEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ3Ljk3MSA0Ny45NzE7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTZweCIgaGVpZ2h0PSIxNnB4Ij4KPGc+Cgk8cGF0aCBkPSJNMjguMjI4LDIzLjk4Nkw0Ny4wOTIsNS4xMjJjMS4xNzItMS4xNzEsMS4xNzItMy4wNzEsMC00LjI0MmMtMS4xNzItMS4xNzItMy4wNy0xLjE3Mi00LjI0MiwwTDIzLjk4NiwxOS43NDRMNS4xMjEsMC44OCAgIGMtMS4xNzItMS4xNzItMy4wNy0xLjE3Mi00LjI0MiwwYy0xLjE3MiwxLjE3MS0xLjE3MiwzLjA3MSwwLDQuMjQybDE4Ljg2NSwxOC44NjRMMC44NzksNDIuODVjLTEuMTcyLDEuMTcxLTEuMTcyLDMuMDcxLDAsNC4yNDIgICBDMS40NjUsNDcuNjc3LDIuMjMzLDQ3Ljk3LDMsNDcuOTdzMS41MzUtMC4yOTMsMi4xMjEtMC44NzlsMTguODY1LTE4Ljg2NEw0Mi44NSw0Ny4wOTFjMC41ODYsMC41ODYsMS4zNTQsMC44NzksMi4xMjEsMC44NzkgICBzMS41MzUtMC4yOTMsMi4xMjEtMC44NzljMS4xNzItMS4xNzEsMS4xNzItMy4wNzEsMC00LjI0MkwyOC4yMjgsMjMuOTg2eiIgZmlsbD0iI0ZGRkZGRiIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=);background-size:18px;display:inline-block;width:18px;height:18px;background-repeat:no-repeat;position:relative;top:10px}
.main-menu ul li.menu-item-has-children:after{content:"";background:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAxMjkgMTI5IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAxMjkgMTI5IiB3aWR0aD0iMTZweCIgaGVpZ2h0PSIxNnB4Ij4KICA8Zz4KICAgIDxwYXRoIGQ9Im0xMjEuMywzNC42Yy0xLjYtMS42LTQuMi0xLjYtNS44LDBsLTUxLDUxLjEtNTEuMS01MS4xYy0xLjYtMS42LTQuMi0xLjYtNS44LDAtMS42LDEuNi0xLjYsNC4yIDAsNS44bDUzLjksNTMuOWMwLjgsMC44IDEuOCwxLjIgMi45LDEuMiAxLDAgMi4xLTAuNCAyLjktMS4ybDUzLjktNTMuOWMxLjctMS42IDEuNy00LjIgMC4xLTUuOHoiIGZpbGw9IiNGRkZGRkYiLz4KICA8L2c+Cjwvc3ZnPgo=) no-repeat;background-size:16px;display:inline-block;width:16px;height:16px;top:10px}
.main-menu ul li.menu-item-has-children:hover:after{-moz-transform:rotate(180deg);-o-transform:rotate(180deg);-webkit-transform:rotate(180deg);-ms-transform:rotate(180deg);transform:rotate(180deg);top:-10px;right:20px}
.main-menu .amp-menu li:hover li:hover>a{background:#333}
.amp-logo h1 {font-weight: normal;font-size: 23px;}
.amp-logo h1 a{color: #ffffff;}

/**** 
* Footer
*****/
.footer{margin:0;font-size:12px;text-align:center;display:inline-block;clear:both;width:100%;height: 100vh;}
.footer .amp-logo{margin-bottom:40px}
.footer .amp-sidebar-button{top:0}
.menu{display:inline-block;text-align:center;border:1px solid #000;margin-top:20%;}
.amp-social li:before{color:#000;transition: all 0.3s ease-in-out 0s;-webkit-transition: all 0.3s ease-in-out 0s;-moz-transition: all 0.3s ease-in-out 0s;-ms-transition: all 0.3s ease-in-out 0s;-o-transition: all 0.3s ease-in-out 0s;}
.amp-social{display:block;padding:0 10%; margin-top:60px;}
.amp-social li{font-size:18px;margin:0 10px 10px 0}
.icon-facebook:before,.icon-google-plus:before,.icon-instagram:before,.icon-linkedin:before,.icon-pinterest:before,.icon-reddit-alien:before,.icon-snapchat-ghost:before,.icon-tumblr:before,.icon-twitter:before,.icon-vk:before,.icon-youtube-play:before{background:0 0}
.rts{font-size:.5rem;margin-top:30px;}
.rts a{color:#000;text-decoration:underline}
.footer .amp-sidebar-toggle{display:none}
.footer .amp-sidebar-button:before{content:"";position:absolute;z-index:-1;top:0;left:0;right:0;bottom:0;
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> background:rgba(255,61,37,1); <?php } ?>-webkit-transform:scaleX(0);transform:scaleX(0);-webkit-transform-origin:0 50%;transform-origin:0 50%;-webkit-transition-property:transform;transition-property:transform;-webkit-transition-duration:0.3s;transition-duration:0.3s;-webkit-transition-timing-function:ease-out;transition-timing-function:ease-out}
.footer .amp-sidebar-button:hover:before,.footer .amp-sidebar-button:focus:before,.footer .amp-sidebar-button:active:before{-webkit-transform:scaleX(1);transform:scaleX(1)}
.footer .amp-sidebar-button:after{content:"Menu";display:inline-block;color:#000;font-size:14px;padding:11px 55px 10px;cursor:pointer;-webkit-transform:perspective(1px) translateZ(0);transform:perspective(1px) translateZ(0);box-shadow:0 0 1px transparent;position:relative;-webkit-transition-property:color;transition-property:color;-webkit-transition-duration:0.3s;transition-duration:0.3s}
.footer .amp-sidebar-button:hover:after{color:#fff;}
.footer .amp-social ul a:hover li:before{
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    color: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> color:rgba(255,61,37,1); <?php } ?>
}

<?php if(is_home() || is_archive()){ ?>
/**** 
* Loop
*****/
.lp{display:inline-block;width:100%;margin:6px 0px}
.feat-img,.lp-list{position:relative;width:100%;text-align:center;max-height:425px;overflow:hidden;background:#000;}
.single .feat-img{height:80vh;width:100%;overflow:hidden;max-height:100vh}
.single{position:relative;text-align:center}
.feat-img a{line-height:0;display:block}
.feat-img .loop-img a amp-img{position:relative;width:100%}
.lp{position:absolute;top:30px;bottom:0;width:100%;left:0;right:0;margin:0 auto}
.lp ul li{font-size:14px;letter-spacing:1.5px;text-transform:uppercase;font-weight:400;list-style:none;display:inline-block;margin-right:10px}
.lp ul li a{color:#fff}
.loop-category li:after{content:"/";display:inline-block;color:#ffffff;padding-left:10px;font-weight:normal}
.loop-category li:last-child{margin-right:0}
.loop-category li:last-child:after{display:none}
.loop-category{display:inline-block;vertical-align:middle;-webkit-transform:perspective(1px) translateZ(0);transform:perspective(1px) translateZ(0);box-shadow:0 0 1px transparent;position:relative}
.loop-category:after{content:"";position:absolute;z-index:-1;left:35%;right:35%;bottom:0;background:#ffffffb8;height:2px;-webkit-transition-property:left, right;transition-property:left, right;-webkit-transition-duration:0.3s;transition-duration:0.3s;-webkit-transition-timing-function:ease-out;transition-timing-function:ease-out;top:180%}
.loop-category:hover:after,.loop-category:focus:after,.loop-category:active:after{left:0;right:0;top:180%}
.lp-list:hover .loop-category:after{left:0;right:0;top:180%}
.lp h2{font-size:30px;font-weight:400;line-height:1.5;margin:45px 0px 0px 0px;font-family:'Libre Baskerville', serif}
.lp h2 a{color:#fff;text-shadow:1px 1px 3px rgba(0, 0, 0, 0.70);}
.lp p{font-size:15px;line-height:26px;color:#fff;margin-top:25px;font-weight:300}
.feat-lay{background:rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .5) 0%) repeat scroll 0 0;bottom:0;left:0;position:absolute;right:0;top:0}

/****
* Pagination
*****/
.loop-pagination{width:100%;display:inline-block;text-align:center;margin:20px 0px;font-size:18px;font-weight:600;letter-spacing:1px}
.loop-pagination .right,.loop-pagination .left{float:none}
.loop-pagination .right a,.loop-pagination .left a{
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    color: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> color:rgba(255,61,37,1); <?php } ?>
position:relative}
.loop-pagination .right a:after{content:"";background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMxLjQ5IDMxLjQ5IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAzMS40OSAzMS40OTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8cGF0aCBkPSJNMjEuMjA1LDUuMDA3Yy0wLjQyOS0wLjQ0NC0xLjE0My0wLjQ0NC0xLjU4NywwYy0wLjQyOSwwLjQyOS0wLjQyOSwxLjE0MywwLDEuNTcxbDguMDQ3LDguMDQ3SDEuMTExICBDMC40OTIsMTQuNjI2LDAsMTUuMTE4LDAsMTUuNzM3YzAsMC42MTksMC40OTIsMS4xMjcsMS4xMTEsMS4xMjdoMjYuNTU0bC04LjA0Nyw4LjAzMmMtMC40MjksMC40NDQtMC40MjksMS4xNTksMCwxLjU4NyAgYzAuNDQ0LDAuNDQ0LDEuMTU5LDAuNDQ0LDEuNTg3LDBsOS45NTItOS45NTJjMC40NDQtMC40MjksMC40NDQtMS4xNDMsMC0xLjU3MUwyMS4yMDUsNS4wMDd6IiBmaWxsPSIjZmYzZDI1Ii8+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=);background-size:18px;display:inline-block;width:20px;height:20px;background-repeat:no-repeat;position:absolute;top:30px;left:0px;right:0px;margin:0 auto}
.loop-pagination .left{position:relative;top:40px;margin-bottom:30px}
.loop-pagination .left a:after{content:"";background-image:url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMxLjQ5NCAzMS40OTQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMxLjQ5NCAzMS40OTQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTZweCIgaGVpZ2h0PSIxNnB4Ij4KPHBhdGggZD0iTTEwLjI3Myw1LjAwOWMwLjQ0NC0wLjQ0NCwxLjE0My0wLjQ0NCwxLjU4NywwYzAuNDI5LDAuNDI5LDAuNDI5LDEuMTQzLDAsMS41NzFsLTguMDQ3LDguMDQ3aDI2LjU1NCAgYzAuNjE5LDAsMS4xMjcsMC40OTIsMS4xMjcsMS4xMTFjMCwwLjYxOS0wLjUwOCwxLjEyNy0xLjEyNywxLjEyN0gzLjgxM2w4LjA0Nyw4LjAzMmMwLjQyOSwwLjQ0NCwwLjQyOSwxLjE1OSwwLDEuNTg3ICBjLTAuNDQ0LDAuNDQ0LTEuMTQzLDAuNDQ0LTEuNTg3LDBsLTkuOTUyLTkuOTUyYy0wLjQyOS0wLjQyOS0wLjQyOS0xLjE0MywwLTEuNTcxTDEwLjI3Myw1LjAwOXoiIGZpbGw9IiNmZjNkMjUiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==);background-size:18px;display:inline-block;width:20px;height:20px;background-repeat:no-repeat;position:absolute;top:30px;left:0px;right:0px;margin:0 auto}

/**** 
* Archive
*****/
.arch-info{padding:30px;margin-top:25px;text-align:center}
.arch-info p{font-style:italic;font-weight:500;font-size:16px;line-height:30px}
.arch-info h3{text-transform:uppercase;font-weight:600;font-size:20px;margin:0px 0px 20px 0px;position:relative}
.arch-info h3:after{content:"";display:block;position:absolute;left:0;right:0;margin:25px auto;width:60px;border-bottom:2px solid #000}
.amp-archive-desc{margin-top:30px}
.amp-archive-desc{margin-top:40px;display:inline-block}

<?php }
if(is_single()){ ?>
/****
* Single
*****/
.rlp{margin-top:50px}
.rlp h3{margin:0;font-size:20px;text-transform:uppercase;padding-bottom:10px;color:#333;letter-spacing:1px}
.rlp .amp-author{display:none}
.rlp ul{list-style-type:none}
.rlp ul li{width:33.3%;position:relative;float:left}
.rlp amp-img{width:100%;display:inline-block;height:auto}
.rlp-image{position:relative;height:230px;overflow:hidden;background:#000;}
.rlp-image a{line-height:0;display:block}
.rlp-cnt{position:absolute;top:20%;bottom:auto;left:15px;right:15px;margin:0 auto;text-align:center}
.related_link{font-size:27px;font-weight:500;line-height:1.5;font-family:'Libre Baskerville', serif}
.related_link a{color:#ffffff}
.related_link p{display:none}
.amp-author amp-img{border-radius:50%;float:left;margin-right:30px}
.amp-author > span{display:block}
.amp-author{width:100%;display:inline-block;margin-top:30px;}
.author-name:before{content:"";display:block;border-top:2px solid #959595;width:65px;padding-bottom:10px}
.author-details,.amp-author-image{display:inline-block;vertical-align:middle}
.author-name{font-size:14px;font-style:italic;font-weight:500;color:#959595;font-family:'Libre Baskerville', serif}
.author-name a{color:#959595;font-style:normal;text-transform:uppercase;padding-left:5px;letter-spacing:1px;text-decoration:underline;font-family:'Poppins', sans-serif}
.single-cntn{ margin-top: 30px;}
.single-cntn p{font-size:15px;color:#222;line-height:26px;font-weight:400}
.single-cntn figure{margin:30px 0px}
.single-cntn ul li{position:relative;padding-left:30px;list-style:none;margin-bottom:12px}
.single-cntn ul li:before{position:absolute;content:"";top:10px;left:10px;margin-top:-3px;width:6px;height:6px;overflow:hidden;
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> background:rgba(255,61,37,1); <?php } ?>}
.single-cntn ol li{display:list-item}
.single-cntn blockquote{background:transparent}
.single-cntn blockquote:before{display:block;width:48px;height:48px;margin-bottom:24px;content:"“";background-color:#1e2122;color:#fff;font-family:'Libre Baskerville', serif;font-weight:600;font-size:60px;line-height:86px;text-align:center}
.loop-date {
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> background:rgba(255,61,37,1); <?php } ?> display: inline-block;margin-top: 30px;color: #fff;font-size: 10px;padding: 7px 20px 4px 20px;text-transform: uppercase;font-weight: 500;letter-spacing: 1px;}
.feat-lay{background:rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .5) 0%) repeat scroll 0 0;bottom:0;left:0;position:absolute;right:0;top:0}
.single .feat-img{height:80vh;width:100%;overflow:hidden;max-height:100vh}
.single{position:relative;text-align:center}
.feat-img a{line-height:0;display:block}
.feat-img .loop-img a amp-img{position:relative;width:100%}
.lp{position:absolute;top:30px;bottom:0;width:100%;left:0;right:0;margin:0 auto}
.lp ul li{font-size:14px;letter-spacing:1.5px;text-transform:uppercase;font-weight:500;list-style:none;display:inline-block;margin-right:10px}
.lp ul li a{color:#fff}
.loop-category li:after{content:"/";display:inline-block;color:#ffffff;padding-left:10px;font-weight:normal}
.loop-category li:last-child{margin-right:0}
.loop-category li:last-child:after{display:none}
.loop-category{position:relative}
.loop-category:after{content:"";display:block;background-color:#fff;height:2px;width:60px;position:absolute;left:0;right:0;margin:20px auto}
.lp h2{font-size:27px;font-weight:400;line-height:1.38;margin:50px 0px 0px 0px;font-family:'Libre Baskerville', serif}
.lp h2 a{color:#fff;text-shadow:1px 1px 3px rgba(0, 0, 0, 0.6)}
.lp p{font-size:16px;line-height:24px;color:#fff;margin-top:25px;font-weight:300}

/** Socila icons **/
.single-sc-ic .amp-social{padding:0;margin:30px 0px 0px 0px}
.single-sc-ic .amp-social li:before{background:#e4dedec7;width:20px;height:20px;color:#333;padding:8px}
.single-sc-ic .amp-social li:hover:before{
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> background:rgba(255,61,37,1); <?php } ?>color:#fff;}
.single .wp-caption-text{z-index:9;background:#ffffffc7;position:absolute;bottom:0;line-height:1.4em}
/**** 
* Comments
*****/
.comments_list ul{margin:0;padding:0}
.comments_list ul.children{padding-bottom:10px;margin-left:4%;width:96%}
.comments_list ul li p{margin:0;font-size:14px;clear:both;padding-top:5px}
.comments_list ul li .says{margin-right:4px}
.comments_list ul li .comment-body{padding:10px 0px 15px 0px}
.comments_list li li{margin:20px 20px 10px 20px;background:#f7f7f7;box-shadow:none;border:1px solid #eee}
.comments_list li li li{margin:20px 20px 10px 20px}
.comment-meta amp-img{border-radius:50%;margin-right:10px;float:left}
.cmts{width:100%;display:inline-block;clear:both;margin-top:40px}
.cmts h3{margin:0;font-size:14px;padding-bottom:5px;border-bottom:2px solid #eee;font-weight:600}
.cmts h3:after{content:"";display:block;width:125px;
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
    border-bottom:2px solid <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
    <?php } else { ?> border-bottom:2px solid rgba(255,61,37,1); <?php } ?>
position:relative;top:7px}
.cmts ul{margin-top:16px}
.cmts ul li{list-style:none;margin-bottom:25px}
.cmts .comment-author.vcard .says{display:none}
.cmts .comment-author.vcard .fn{font-size:14px;font-weight:600;color:#111}
.cmts .comment-metadata{font-size:11px;font-weight:400}
.cmts .comment-metadata a{color:#333}
.comment-content{margin-top:10px}
.comment-content p{font-size:12px;color:#000;line-height:19px}
.amp-comment-button a{color:#111;border:1px solid #000;display:inline-block;padding:9px 32px 10px;-webkit-transition-property:color;transition-property:color;-webkit-transition-duration:0.3s;transition-duration:0.3s}
.amp-comment-button a:before{content:"";position:absolute;z-index:-1;top:0;left:0;right:0;bottom:0;-webkit-transform:scaleX(0);transform:scaleX(0);-webkit-transform-origin:0 50%;transform-origin:0 50%;-webkit-transition-property:transform;transition-property:transform;-webkit-transition-duration:0.3s;transition-duration:0.3s;-webkit-transition-timing-function:ease-out;transition-timing-function:ease-out;
<?php if($redux_builder_amp['minimalblog-color-scheme']['rgba']){?>
background: <?php echo $redux_builder_amp['minimalblog-color-scheme'] ['rgba'] ?>;
<?php } else { ?> background:rgba(255,61,37,1); <?php } ?> }
.amp-comment-button a:hover{color:#fff}
.amp-comment-button{font-size:14px;margin:0 auto;text-align:center;width:100%}
.amp-comment-button a{display:inline-block;-webkit-transform:perspective(1px) translateZ(0);transform:perspective(1px) translateZ(0);box-shadow:0 0 1px transparent;position:relative}
.amp-comment-button a:hover:before,.amp-comment-button a:focus:before,.amp-comment-button a:active:before{-webkit-transform:scaleX(1);transform:scaleX(1)}
<?php } ?>

.redux-preview-image {
    max-height: 250px;
    padding: 5px;
    margin-top: 10px;
    border: 1px solid #e3e3e3;
    background: #f7f7f7;
    border-radius: 3px;
}
@media (max-width:768px){
.single .amp-featured-image amp-img{width:175%;height:100%}
.rlp ul li {width: 100%;float: none}
.rlp-image{position:relative;height:auto;overflow:hidden}
.rlp-image {height: 250px;}
}
@media (max-width:425px){
.related_link {font-size: 23px;}
.rlp-image {height: auto;}
.cntr{padding:0px 15px;}
.loop-wrapper .cntr{padding:0px 25px;}
.lp ul li{font-size:11px}
.loop-category li:after{color:#ffffff7a}
.loop-category:after{height:1px;top:150%}
.single .loop-category:after{top:16px}
.lp h2{margin:25px 0px 0px 0px}
.lp p{font-size:13px;color:#fffc;}
.amp-logo h1 {
    font-size: 21px;
    position: relative;
    top: 14px;
    margin: 0 0 0 5px;
}
.amp-sidebar-button {top: 3px;}
}

@media (max-width:320px){
.loop-category:after{top:150%}
.lp-list:hover .loop-category:after{top:150%}
.lp h2{font-size:22px}
.related_link {font-size: 18px;}
}

/****
* RTL Styles
*****/
    <?php  if( is_rtl() ) {?> <?php } ?>
