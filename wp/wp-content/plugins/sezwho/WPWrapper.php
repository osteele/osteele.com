<?php
	/*
		This Class provide wrapper methods over the WP API. 
		The plugin will invoke these methods and not the WP API methods directly.
		Author: Comment Power
		Date : 02/10/06
	*/

        if ( !defined('ABSPATH') )   {
		// include wp-config as it has the ABSPATH initialization
		require_once('../../../wp-config.php');
        }
    // these are included as functions from within these are called in the wrapper / plug-in
	require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
        require_once(ABSPATH . '/wp-config.php');
	require_once(ABSPATH . '/wp-includes/version.php');
	if(strpos($wp_version,"2.0") !== FALSE)
	{
		require_once(ABSPATH . '/wp-includes/comment-functions.php'); 
	}
	else 
	{
		require_once(ABSPATH . '/wp-includes/comment-template.php'); 
		require_once(ABSPATH . '/wp-includes/comment.php'); 
	}
	       
	class WPWrapper    
	{	    
		/*
			This returns the instance of the WPWrapper. This function make sures that there is only one instance of WPWrapper in the application
		*/
	function getInstance()	{
		if($_this->instance == NULL)  {
			$_this->instance = new WPWrapper();
		}
		return $_this->instance;
	}        

        function yk_get_var($sql_query){            
            global $wpdb ;
            return $wpdb->get_var($sql_query) ;    
        }
        		
        function yk_get_num_rows($sql_query){            
            global $wpdb ;
            $result = $wpdb->query($sql_query) ;    
            return $wpdb->num_rows ;
        }	            
        
        function yk_get_results($sql_query){
            global $wpdb ;
            return $wpdb->get_results($sql_query) ;                
        }
        
        function yk_get_row($sql_query){            
            global $wpdb ;
            return $wpdb->get_row($sql_query) ;                        
        }
        
        function yk_query($sql_query){
            global $wpdb ;
            return $wpdb->query($sql_query) ;                        
        }
        
        function yk_get_global_table_prefix(){
            global $wpdb ;
            return $wpdb ;
        }
        
        function yk_num_rows($sql_query){
            global $wpdb ;
            $wpdb->query($sql_query);
            return $wpdb->num_rows ;            
        }
        
        /*
        This is a DDL function to create table
        */
        function yk_maybe_create_table($table_name, $table_ddl_sql){
            global $wpdb ;
            maybe_create_table($wpdb->$table_name, $table_ddl_sql);
        }
        
        function yk_get_commentdata($comment_id, $iscache, $include_unapproved) {
            return get_commentdata($comment_id, $iscache, $include_unapproved);
        }
        
        function yk_get_postdata($postid) {
            return get_commentdata($postid);
        }
        
        function yk_dbDelta($sql_query){
            return dbDelta($sql_query) ; 
        }    

	function yk_getPluginDBName(){
	    return get_option('CPPLUGINDB');
	}
        /**
        * This method returns the comments sorted by the sort order passed. It follows the following logic: 
        *   1. Query cp_schema.comment table to get the sorted comments as per sort order passed
        *   2. Use WP API to query for comment using the comment_id obtained from step 1       
        *   3. If there are comments that were posted before installation of the plug-in do another query to include them 
        */
        function  yk_get_sorted_comments($post_id, $sortOrder){    
            if ($sortOrder) {
                global $wpdb ;
                $sort_param_arr = explode( ":" , $sortOrder);
                //print_r($sort_param_arr);
                $sort_param = $sort_param_arr[0] ;
                $sort_order = $sort_param_arr[1] ;
                // echo(' sortOrder = '.$sortOrder.' : sort_param_arr = '.$sort_param_arr.' : sort_param = '.$sort_param.' : sort_order = '.$sort_order);
                if ($sort_param == 'commentScore') {// for sorting by comment score                
                    $query = "SELECT comment_id FROM sz_comment WHERE posting_id = '$post_id' order by comment_score $sort_order";
                    $sorted_comment_ids = $wpdb->get_results($query); 
                    $sorted_comments_to_return ;
                    $rated_comment_ids ;
                    $count = 0 ;               
                    if ($sorted_comment_ids) {
                        foreach ($sorted_comment_ids as $comment_id_arr) {                              
                            $comment_id = $comment_id_arr->comment_id ;    
                            if ($count == 0) {
                                $rated_comment_ids =  $comment_id ;
                            } else {
                                $rated_comment_ids =  $rated_comment_ids.','.$comment_id ;
                            }                        
	                        $comment = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post_id and comment_id = $comment_id ");                                
                            $sorted_comments_to_return[$count] = $comment[0] ;
                            $count ++ ;
                        }  
                        $unrated_comment_query = "SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post_id and comment_id NOT IN ($rated_comment_ids) order by comment_date desc" ;
                        $unrated_comment = $wpdb->get_results($unrated_comment_query);            
                        $unrated_comment_num = $wpdb->num_rows ;                                                                                
                        for ($unrated_count = 0 ; $unrated_count < $unrated_comment_num ; $unrated_count++) {
                            $sorted_comments_to_return[$count] = $unrated_comment[$unrated_count] ;
                            $count++ ;
                        }           
                        return $sorted_comments_to_return ;   
                    } else {  // in case there has are comments that are posted before installing the plug-in, just get the approved comments
                        return get_approved_comments($post_id) ; 
                    }    
                }  else if ($sort_param == 'date') {
                    $approved_comments_desc_date = get_approved_comments($post_id) ; 
                    if ($sort_order == 'asc') {
                        return $approved_comments_desc_date ;
                    } else {
                        $approved_comments_asc_date = array() ;
                        $arr_count = count($approved_comments_desc_date) ;
                        for ($co = count($approved_comments_desc_date); $co > 0 ; $co--) {                            
                            $approved_comments_asc_date[$arr_count-$co] =  $approved_comments_desc_date[$co-1] ;                            
                        }
                        return  $approved_comments_asc_date ;
                    }                                                   
                } 
                 else {  // if sortOrder is null, return the approved comments
                    return get_approved_comments($post_id) ;
                } 
            }
        }	
    }
?>
