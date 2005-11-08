<?php
/*
Plugin Name: References
Plugin URI: http://movabletripe.com/archive/references-plugin/
Description: Generates a list of references relating to any given post. Lists are generated from the post-meta (a.k.a 'Custom Fields').
Author: Adam Hennessy
Author URI: http://movabletripe.com/
Version: 0.3.1 beta
*/


function post_references($post_reference_title = "References", $before = '', $after = '')
{
        $post_reference = get_post_custom_values("reference");
        if(count($post_reference)>0)
        {
                $reference_title = trim($post_reference);
                echo "$before<h2><a href=\"";
                	the_permalink();
                echo "#references\" rel=\"bookmark\" title=\"$post_reference_title\">$post_reference_title</a></h2>
							<ul>";
                for($i=0;$i<count($post_reference);$i++)
                {
                        echo "<li>".trim($post_reference[$i])."</li>";
                }
                echo "</ul>$after\n";
        }
}

function post_references_link($post_reference_title = "References", $before = '', $after = '')
{
      $post_reference = get_post_custom_values("reference");
        if(count($post_reference)>0)
        {
                echo "$before<a href=\"";
                	the_permalink();
                echo "#references\" rel=\"bookmark\" title=\"$post_reference_title\">$post_reference_title</a>$after";
		  }
}
?>
