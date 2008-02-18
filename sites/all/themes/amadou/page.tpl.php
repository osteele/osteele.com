<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language ?>" lang="<?php print $language ?>">
  <head>
    <title><?php print $head_title ?></title>
    <?php print $head ?>
    <?php print $styles ?>
    <?php print $scripts ?>
      <!--[if IE 6]>
        <style type="text/css" media="all">@import "<?php print base_path() . path_to_theme() ?>/ie-fixes/ie6.css";</style>
      <![endif]-->
      <!--[if lt IE 7.]>
        <script defer type="text/javascript" src="<?php print base_path() . path_to_theme() ?>/ie-fixes/pngfix.js"></script>
      <![endif]-->
  </head>

  <body>

    <!-- begin container -->
    <div id="container">

      <!-- primary links -->
      <div id="menu">
        <?php if (isset($primary_links)) : ?>
          <?php print theme('links', $primary_links) ?>
        <?php endif; ?>
      </div><!-- end primary links -->

      <!-- begin header -->

      <div id="header">

        <!-- site logo -->
        <?php if ($logo) : ?>
          <a href="<?php print $base_path ?>" title="<?php print t('Home') ?>">
            <img class="logo" src="<?php print $logo ?>" alt="<?php print t('Home') ?>" />
          </a>
        <?php endif; ?><!-- end site logo -->

        <!-- site name -->
        <?php if ($site_name) : ?>
          <h1>
	    <a href="<?php print $base_path ?>" title="<?php print t('Home') ?>">
	      <?php print $site_name; ?>
	    </a>
	  </h1>
        <?php endif; ?><!-- end site name -->
	  
        <!-- site slogan -->
        <?php if ($site_slogan) : ?>
          <h2>
	    <?php print $site_slogan; ?>
	  </h2>
        <?php endif; ?><!-- end site slogan -->

      </div><!-- end header -->

      <!-- content -->

      <!-- begin mainContent -->
      <div id="mainContent" style="width: <?php print amadou_get_mainContent_width( $sidebar_left, $sidebar_right) ?>px;">
        
        <?php if ($mission): print '<div class="mission">'. $mission .'</div>'; endif; ?>
        <?php if ($breadcrumb): print '<div class="breadcrumb">'. $breadcrumb . '</div>'; endif; ?>
        <?php if ($title) : print '<h1 class="pageTitle">' . $title . '</h1>'; endif; ?>
        <?php if ($tabs) : print '<div class="tabs">' . $tabs . '</div>'; endif; ?>
        <?php if ($help) : print '<div class="help">' . $help . '</div>'; endif; ?>
        <?php if ($messages) : print '<div class="messages">' .$messages . '</div>'; endif; ?>
        <?php print $content_top; ?>
        <?php print $content; ?>
        <?php print $content_bottom; ?>
        <?php print $feed_icons; ?>

      </div><!-- end mainContent -->

      <!-- begin sideBars -->

      <div id="sideBars-bg" style="width: <?php print amadou_get_sideBars_width( $sidebar_left, $sidebar_right) ?>px;">
        <div id="sideBars" style="width: <?php print amadou_get_sideBars_width( $sidebar_left, $sidebar_right) ?>px;">

          <!-- left sidebar -->
          <?php if ($sidebar_left) : ?>
            <div id="leftSidebar">
              <?php print $sidebar_left; ?>
            </div>
          <?php endif; ?>
        
          <!-- right sidebar -->
          <?php if ($sidebar_right) : ?>
            <div id="rightSidebar">
              <?php print $sidebar_right; ?>
            </div>
          <?php endif; ?>

        </div><!-- end sideBars -->
      </div><!-- end sideBars-bg -->
    
      <!-- footer -->
      <div id="footer">
        <?php print $footer_message; ?>
        <?php print $footer; ?>
      </div><!-- end footer -->
    
    </div><!-- end container -->
  
    <?php print $closure ?>
  </body>
</html>
