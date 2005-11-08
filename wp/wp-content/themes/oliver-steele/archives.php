<?php require('../../../wp-blog-header.php'); ?>

<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="content" class="widecolumn">

<!--<?php include (TEMPLATEPATH . '/searchform.php'); ?>-->

<!--
<h2>Archives by Month:</h2>
  <ul>
    <?php wp_get_archives('type=monthly'); ?>
  </ul>

<h2>Archives by Subject:</h2>
  <ul>
     <?php wp_list_cats(); ?>
  </ul>
-->

<h2>Categories</h2>
<? af_ela_super_archive(); ?>
<div style="clear: both"></div>

<h2>Tag Cloud</h2>
<?php UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud","",0) ?>

<h2>Top Tags</h2>
<?php UTW_ShowWeightedTagSet("weightedlongtailvertical","",10) ?>

</div>	

<?php get_footer(); ?>
