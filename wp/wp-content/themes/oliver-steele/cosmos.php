<?php require('../../../wp-blog-header.php'); ?>

<?php 
/*
Template Name: CosmosPage
Description:  Used for tags cosmos
*/

// JL - this code block is a quick hack to add the cosmos styles
//      for best results, copy the styles into your stylesheet and delete this block
function echo_cosmos_styles() {
	
	echo <<< COSMOSEND
	
	<style type="text/css" media="screen">
		#content ul.cosmos {
			margin: 2em 0;
			list-style: none;
			font-size: 100%;
			}
			
		#content li.cosmos {
			display: inline;
			padding: 0;
			margin: 4px;
			line-height: 2em;
			}
		
		#content li.keyword1 { font-size: 0.7em; }
		#content li.keyword2 { font-size: 0.9em; }
		#content li.keyword3 { font-size: 1.0em; }
		#content li.keyword4 { font-size: 1.1em; }
		#content li.keyword5 { font-size: 1.2em; }
		#content li.keyword6 { font-size: 1.3em; }
		#content li.keyword7 { font-size: 1.4em; }
		#content li.keyword8 { font-size: 1.5em; }
		#content li.keyword9 { font-size: 1.6em; }
		#content li.keyword10 { font-size: 1.7em; }
		#content li.keyword11 { font-size: 1.8em; }
		#content li.keyword12 { font-size: 1.9em; }
		#content li.keyword13 { font-size: 2.0em; }
		#content li.keyword14 { font-size: 2.1em; }
		#content li.keyword15 { font-size: 2.2em; }
	</style>
	
COSMOSEND;
}
add_filter('wp_head', 'echo_cosmos_styles');

// JL - end block

get_header(); ?>

	<div id="content" class="narrowcolumn">

		<ul class="cosmos">
			<?php all_keywords('<li class="cosmos keyword%count%"><a href="/tag/%keylink%">%keyword%</a></li>','<li class="cosmos keyword%count%"><a href="%keylink%">%keyword%</a></li>'); ?>
		</ul>
		
		<p>Created with <a href="http://vapourtrails.ca/wp-plugins" title="Jerome's Keywords plugin">Jerome's Keywords</a> (1.5-alpha)</p>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>