<?php

/*
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

For full license details see license.txt
============================================================================================================ */

include ('../../../wp-config.php');


class AdvancedPermalinkAJAX extends Advanced_Permalinks_Plugin
{
	function AdvancedPermalinkAJAX ()
	{
		if (!current_user_can ('administrator'))
			die ('<p style="color: red">You are not allowed access to this resource</p>');
		
		$_POST = stripslashes_deep ($_POST);
		
		$command = $_POST['cmd'];
		$id      = $_POST['id'];
		
		$this->register_plugin ('headspace', __FILE__);
		if (method_exists ($this, $command))
			$this->$command ($id);
		else
			die ('<p style="color: red">That function is not defined</p>');
	}
	
	function delete ($id)
	{
		$links = Advanced_Permalinks::get ();
		if ($links->remove_permalink ($id))
			echo 'OK';
		else
			echo 'FAIL';
	}
	
	function edit ($id)
	{
		$links = Advanced_Permalinks::get ();
		$perma = $links->get_post_permalinks ();
		
		if (isset ($perma[$id]))
			$this->render_admin ('add', array ('start' => $id, 'end' => $perma[$id]['end'], 'link' => $perma[$id]['link'], 'edit' => true));
	}
	
	function cancel ($id)
	{
		$links = Advanced_Permalinks::get ();
		$perma = $links->get_post_permalinks ();
		
		if (isset ($perma[$id]))
			$this->render_admin ('permalinks_item', array ('link' => $perma[$id], 'start' => $id));
	}
	
	function save ($id)
	{
		$links = Advanced_Permalinks::get ();
		$perma = $links->get_post_permalinks ();
		
		if (isset ($perma[$id]))
		{
			$links->remove_permalink ($id);
			$links->create_permalink (intval ($_POST['start']), intval ($_POST['end']), $_POST['permalink']);
			
			$this->render_admin ('permalinks', array ('links' => $links->get_post_permalinks ()));
		}
	}
	
	function delete_migration ($id)
	{
		$migrations = get_option ('advanced_permalinks_migration');
		if (isset ($migrations[$id]))
		{
			unset ($migrations[$id]);
			echo 'OK';
		}
		else
			echo 'FAIL';
		update_option ('advanced_permalinks_migration', $migrations);
	}
	
	function edit_migration ($id)
	{
		$migrations = get_option ('advanced_permalinks_migration');
		if (isset ($migrations[$id]))
			$this->render_admin ('migrate_edit', array ('migration' => $migrations[$id], 'pos' => $id));
	}
	
	function save_migrate ($id)
	{
		$migrations = get_option ('advanced_permalinks_migration');
		$migrations[$id] = $_POST['permalink'];
		update_option ('advanced_permalinks_migration', $migrations);
	}
	
	function show_migrate ($id)
	{
		$migrations = get_option ('advanced_permalinks_migration');
		$this->render_admin ('migrate_item', array ('link' => $migrations[$id], $id));
	}
}


$obj = new AdvancedPermalinkAJAX ();

?>