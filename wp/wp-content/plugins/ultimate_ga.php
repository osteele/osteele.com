<?php
/*
Plugin Name: Ultimate Google Analytics
Plugin URI: http://www.oratransplant.nl/uga
Description: Enable Google Analytics on your blog. Has options to also track external links, mailto links and links to downloads on your own site. Check <a href="http://www.oratransplant.nl/uga/#versions">http://www.oratransplant.nl/uga/#versions</a> for version updates
Version: 1.2
Author: Wilfred van der Deijl
Author URI: http://www.oratransplant.nl/about
*/

/*  Copyright 2006 Wilfred van der Deijl  (email : wilfred _at_ vanderdeijl.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
  Version History
    + = new/improved feature
    ! = fixed bug
    - = removed feature
  
  version 0.1
    Initial version
    
  version 0.2
    !: Prevent two consecutive forward slashes in the virtual path for a 
       download link if the URL for the link started with a forward slash
       (e.g. /files/picture.jps would becomde /downloads//files/picture.jpg)
    +: Default value for internal hostnames is no longer just the hostname
       of the current webserver. If this hostnames starts with www. the
       name without www is also added to the internal hostnames
       (e.g. "www.oratransplant.nl,oratransplant.nl")
    +: Renamed track_user to uga_track_user so all functions are prefixed
       with uga to lower the chances of a naming conflict with another
       plugin    
    +: Small HTML comment is placed before the Google Analytics tracker
       to show it was inserted by the Ultimate Google Analytics plugin
    +: Debugging has been added and can be enabled/disabled from the 
       Options page. It is disabled by default. If enabled, all debugging
       info will be added as HTML comment in the footer of the page   
       
  version 1.0
    +: Added filter to process links in the footer of a comment showing
       the link to an author. This enables tracking of these outbound links
       as well
    +: Added phps as an extension for download tracking. Existing 
       installations of Ultimate Google Analytics will not be affected.
       This only applies to the default settings on a fresh install.
    +: If debugging is disabled an empty dummy function is created for
       debugging to improve performance
    +: Added "secret" option to force debugging directly to the output
       stream and not rely on WordPress actions being called. This can
       be helpfull when using a WordPress theme that does not call the
       actions as it shoud  
    +: If content filtering is enabled, also add the filter for outbound, 
       mailto and download tracking to the "the_excerpt" filter for pages 
       showing only an excerpt and not the full article
    !: The "Enable Tracker" option was not saved to the database. Disabling
       the checkbox had no effect
    +: Created a new function uga_set_option to save options to the database
    +: The plugin now detects if the wp_footer action hook is called. Some
       WordPress themes out there do not call this hook as they should.
       If UGA detects this action cannot be hooked, the Google Analytics
       code is added to the <head> section. This can delay the loading of
       your pages (see http://www.websiteoptimization.com/speed/tweak/delay/)
       When the tracker is in the head section the page will not be rendered
       by your browser until the script is executed. That is why Ultimate
       Google Analytics will place the tracker at the end of the <body> 
       section whenever possible.

  version 1.1
    !: The first page that is requested after some other user requested a
       feed had the tracker code in the header in stead of the footer, even
       if the Theme does support the footer action hook.
    !: Corrected two typing errors in debug output
    
  version 1.2
    !: If a page was requested that did not call both the header and the 
       footer hook, UGA would conclude that the footer hook is not implemented
       in your template. UGA would then revert to using the header hook to
       put the tracking javascript. On the next page request that does
       call both the head and footer hook, UGA would detect this and switch
       back to putting the tracking code in footer.
       On my blog this happened with the statistics page produced by 
       wp-stattraq. That page doesn't call either the head or footer hooked.
       Before v1.2 UGA would just look if the footer was called and draw 
       conclusions from that. Now UGA checks execution of both the
       header and footer hook. If none of these are executed it doesn't
       switch its behaviour. It only switches to head if a page is requested
       that does call the wp_head but does not call wp_footer
    !: In the admin page, there was no space between "checked" and the closing
       /> for all checkboxes. Apparently this caused problems when using
       Safari
       
*/

// Uncomment the following line to force debugging regardless setting in
// the Control Panel. With this forced debugging, the info will be written
// directly to the HTML and the plugin will not rely on any WordPress hooks
// This can break your HTML code
// define('uga_force_debug', 'enabled', true);

// add debugging statement to the debug info
// function is an empty dummy function is debugging is disabled
$uga_options = get_option('ultimate_ga_options'); 
$uga_debug_enabled=$uga_options['debug'];
if (defined('uga_force_debug')) {
  // force debugging
  function uga_debug($message) {
    global $uga_debug;
    $uga_debug .= "$message\n";
    echo "<!-- \nUGA_DEBUG: $message\n -->";
  }
} else if ($uga_debug_enabled) {
  // normal debugging is enabled
  function uga_debug($message) {
    global $uga_debug;
    $uga_debug .= "$message\n";
  }
} else {
  // no debugging
  function uga_debug($message) {
  }
}

// set an Ultimate GA option in the options table of WordPress
function uga_set_option($option_name, $option_value) {
  uga_debug ("Start uga_set_option: $option_name, $option_value");
  // first get the existing options in the database
  $uga_options = get_option('ultimate_ga_options');
  // set the value
  $uga_options[$option_name] = $option_value;
  // write the new options to the database
  update_option('ultimate_ga_options', $uga_options);
  uga_debug ('End uga_set_option');
}

// get an Ultimate GA option from the WordPress options database table
// if the option does not exist (yet), the default value is returned
function uga_get_option($option_name) {
  uga_debug("Start uga_get_option: $option_name");

  // get options from the database
  $uga_options = get_option('ultimate_ga_options'); 
  uga_debug('uga_options: '.var_export($uga_options,true));
  
  if (!$uga_options || !array_key_exists($option_name, $uga_options)) {
    // no options in database yet, or not this specific option 
    // create default options array
    uga_debug('Constructing default options array');
    $uga_default_options=array();
    $uga_default_options['internal_domains']  = $_SERVER['SERVER_NAME'];
    if (preg_match('@www\.(.*)@i', $uga_default_options['internal_domains'], $parts)>=1) {
      $uga_default_options['internal_domains'] .= ','.$parts[1];
    }
    $uga_default_options['account_id']             = 'UA-XXXXXX-X';  
    $uga_default_options['enable_tracker']         = true;  
    $uga_default_options['track_adm_pages']        = false;  
    $uga_default_options['ignore_users']           = true;  
    $uga_default_options['max_user_level']         = 8;  
  
    $uga_default_options['footer_hooked']          = false; // assume the worst
    $uga_default_options['filter_content']         = true;  
    $uga_default_options['filter_comments']        = true;  
    $uga_default_options['filter_comment_authors'] = true;  
    $uga_default_options['track_ext_links']        = true;  
    $uga_default_options['prefix_ext_links']       = '/outgoing/';  
    $uga_default_options['track_files']            = true;  
    $uga_default_options['prefix_file_links']      = '/downloads/';  
    $uga_default_options['track_extensions']       = 'gif,jpg,jpeg,bmp,png,pdf,mp3,wav,phps,zip,gz,tar,rar,jar,exe,pps,ppt,xls,doc';  
    $uga_default_options['track_mail_links']       = true;  
    $uga_default_options['prefix_mail_links']      = '/mailto/';  
    $uga_default_options['debug']                  = false;  
    uga_debug('uga_default_options: '.var_export($uga_default_options,true));
    // add default options to the database (if options already exist, 
    // add_option does nothing
    add_option('ultimate_ga_options', $uga_default_options, 
               'Settings for Ultimate Google Analytics plugin');

    // return default option if option is not in the array in the database
    // this can happen if a new option was added to the array in an upgrade
    // and the options haven't been changed/saved to the database yet
    $result = $uga_default_options[$option_name];

  } else {
    // option found in database
    $result = $uga_options[$option_name];
  }
  
  uga_debug("Ending uga_get_option: $option_name ($result)");
  return $result;
}

// function that is added as an Action to ADMIN_MENU
// it adds an option subpage to the options menu in WordPress administration
function uga_admin() {
  uga_debug('Start uga_admin');
  if (function_exists('add_options_page')) {
    uga_debug('Adding options page');
    add_options_page('Ultimate Google Analytics' /* page title */, 
                     'Ultimate GA' /* menu title */, 
                     8 /* min. user level */, 
                     basename(__FILE__) /* php file */ , 
                     'uga_options' /* function for subpanel */);
  }
  uga_debug('End uga_admin');
}

// displays options subpage to set options for Ultimate GA and save any
// changes to these options back to the database
function uga_options() {
  uga_debug('Start uga_options');
  if (isset($_POST['info_update'])) {
    uga_debug('Saving posted options: '.var_export($_POST, true));
    ?><div class="updated"><p><strong><?php 
    // process submitted form
    $uga_options = get_option('ultimate_ga_options');
    $uga_options['account_id']             = $_POST['account_id'];
    $uga_options['internal_domains']       = $_POST['internal_domains'];
    $uga_options['max_user_level']         = $_POST['max_user_level'];
    $uga_options['prefix_ext_links']       = $_POST['prefix_ext_links'];
    $uga_options['prefix_mail_links']      = $_POST['prefix_mail_links'];
    $uga_options['prefix_file_links']      = $_POST['prefix_file_links'];
    $uga_options['track_extensions']       = $_POST['track_extensions'];

    $uga_options['enable_tracker']         = ($_POST['enable_tracker']=="true"          ? true : false);
    $uga_options['filter_content']         = ($_POST['filter_content']=="true"          ? true : false);
    $uga_options['filter_comments']        = ($_POST['filter_comments']=="true"         ? true : false);
    $uga_options['filter_comment_authors'] = ($_POST['filter_comment_authors']=="true"  ? true : false);
    $uga_options['track_adm_pages']        = ($_POST['track_adm_pages']=="true"         ? true : false);
    $uga_options['track_ext_links']        = ($_POST['track_ext_links']=="true"         ? true : false);
    $uga_options['track_mail_links']       = ($_POST['track_mail_links']=="true"        ? true : false);
    $uga_options['track_files']            = ($_POST['track_files']=="true"             ? true : false);
    $uga_options['ignore_users']           = ($_POST['ignore_users']=="true"            ? true : false);
    $uga_options['debug']                  = ($_POST['debug']=="true"                   ? true : false);
    update_option('ultimate_ga_options', $uga_options);
    
    // add/remove filter immediately for admin page currently being rendered
    if (uga_get_option('track_adm_pages')) {
      add_action('admin_footer', 'uga_adm_footer_track');
    } else {
      remove_action('admin_footer', 'uga_adm_footer_track');
    }
    
    _e('Options saved', 'uga')
    ?></strong></p></div><?php
	} 
	
	// show options form with current values
	uga_debug('Showing options page with UGA options');
	?>
<div class=wrap>
  <form method="post">
    <h2>Ultimate Google Analytics</h2>
    <fieldset name="general">
      <legend><?php _e('General settings', 'uga') ?></legend>
      <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Account ID', 'uga') ?></th>
          <td><input name="account_id" type="text" id="account_id" value="<?php echo uga_get_option('account_id'); ?>" size="50" />
            <br />Enter your Google Analytics account ID. Google Analytics supplies you with a snippet of JavaScript to put on
            your webpage. In this JavaScript you can see your account ID in a format like UA-999999-9. There is no need to actually
            include this JavaScript yourself on any page. That's all handled by Ultimate Google Analytics.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Enable tracker', 'uga') ?></th>
          <td><input type="checkbox" name="enable_tracker" id="enable_tracker" value="true" <?php if (uga_get_option('enable_tracker')) echo "checked"; ?> />
            <br />By unchecking this checkbox no JavaScript will be included on the page. It is basically the
            same as disabling the whole plugin
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Track admin pages', 'uga') ?></th>
          <td><input type="checkbox" name="track_adm_pages" id="track_adm_pages" value="true" <?php if (uga_get_option('track_adm_pages')) echo "checked"; ?> />
            <br />Enable or disable the inclusion of Google Analytics tracking on the admin pages of Wordpress.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Ignore logged on users', 'uga') ?></th>
          <td><input type="checkbox" name="ignore_users" id="ignore_users" value="true" <?php if (uga_get_option('ignore_users')) echo "checked"; ?> />
            of level <input name="max_user_level" type="text" id="max_user_level" value="<?php echo uga_get_option('max_user_level'); ?>" size="2" /> and above
            <br />Check this checkbox and specify a user level to ignore users of a particular level or above. For such users the
              Google Analytics JavaScript will not be included in the page
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Enable debugging', 'uga') ?></th>
          <td><input type="checkbox" name="debug" id="debug" value="true" <?php if (uga_get_option('debug')) echo "checked"; ?> />
            <br />Enable or disable debugging info. If enabled, UGA debugging is written as HTML comments
              to the page being rendered.
          </td>
        </tr>
      </table>
    </fieldset>
    
    <fieldset name="external">
      <legend><?php _e('Links tracking', 'uga') ?></legend>
      <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Filter content', 'uga') ?></th>
          <td><input type="checkbox" name="filter_content" id="filter_content" value="true" <?php if (uga_get_option('filter_content')) echo "checked"; ?> />
            <br />Enable or disable tracking of links in the content of your articles. Which type(s) of links
            should be tracked can be selected with the other options. If you plan to disable all of them, you
            are better of disabling the entire filtering to save performance.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Filter comments', 'uga') ?></th>
          <td><input type="checkbox" name="filter_comments" id="filter_comments" value="true" <?php if (uga_get_option('filter_comments')) echo "checked"; ?> />
            <br />Enable or disable tracking of links in the comments. Which type(s) of links
            should be tracked can be selected with the other options. If you plan to disable all of them, you
            are better of disabling the entire filtering to save performance.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Filter comment author links', 'uga') ?></th>
          <td><input type="checkbox" name="filter_comment_authors" id="filter_comment_authors" value="true" <?php if (uga_get_option('filter_comment_authors')) echo "checked"; ?> />
            <br />Enable or disable tracking of links in the comments footer showing the author. 
              If you plan to disable all filters, you are better of disabling the entire filtering to save performance.
          </td>
        </tr>

        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Track external links', 'uga') ?></th>
          <td><input type="checkbox" name="track_ext_links" id="track_ext_links" value="true" <?php if (uga_get_option('track_ext_links')) echo "checked"; ?> />
            and prefix with <input name="prefix_ext_links" type="text" id="prefix_ext_links" value="<?php echo uga_get_option('prefix_ext_links'); ?>" size="40" />
            <br />Include code to track links to external sites and specify what prefix should be used in the
              tracking URL. This groups all your external links in a separate directory when looking at your
              Google Analytics stats
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Internal host(s)', 'uga') ?></th>
          <td><input name="internal_domains" type="text" id="internal_domains" value="<?php echo uga_get_option('internal_domains'); ?>" size="50" />
            <br />Hostname(s) that are considered internal links. Links to these hosts are not tagged as external link.
              You can specify multiple hostnames separated by commas. This list of internal hostnames is also used
              for tagging download links (see below). Download links have to be of a specified file type and it has
              to an internal link. An internal link can either be a relative link (without a hostname) or a link that starts 
              with any of the specified internal hostnames.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Track download links', 'uga') ?></th>
          <td><input type="checkbox" name="track_files" id="track_files" value="true" <?php if (uga_get_option('track_files')) echo "checked"; ?> />
            and prefix with <input name="prefix_file_links" type="text" id="prefix_file_links" value="<?php echo uga_get_option('prefix_file_links'); ?>" size="40" />
            <br />Include code to track internal (within your own site) links to certain file types and specify what prefix should be used in the
              tracking URL. This groups all your file links in a separate directory when looking at your
              Google Analytics stats
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('File extensions to track', 'uga') ?></th>
          <td><input name="track_extensions" type="text" id="track_extensions" value="<?php echo uga_get_option('track_extensions'); ?>" size="50" />
            <br />Specify which file extensions you want to check when download link tracking is enabled.
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Track mailto: links', 'uga') ?></th>
          <td><input type="checkbox" name="track_mail_links" id="track_mail_links" value="true" <?php if (uga_get_option('track_mail_links')) echo "checked"; ?> />
            and prefix with <input name="prefix_mail_links" type="text" id="prefix_mail_links" value="<?php echo uga_get_option('prefix_mail_links'); ?>" size="40" />
            <br />Include code to track mailto: links to email addresses and specify what prefix should be used in the
              tracking URL. This groups all your mail links in a separate directory when looking at your
              Google Analytics stats
          </td>
        </tr>
      </table>
    </fieldset>
    
    <div class="submit">
      <input type="submit" name="info_update" value="<?php _e('Update options', 'uga') ?>" />
	  </div>
  </form>
</div><?php
  uga_debug('End uga_options');
}

// returns true if current user has to be tracked by UGA
// return false if user does not have to be tracked. This is the case when
// the 'ignore_users' option is enabled and the current userlevel is
// equal or higher than the set limit.
function uga_track_user() {
  global $user_level;
  uga_debug('Start uga_track_user');
  if (!user_level) {
    // user nog logged on -> track
    uga_debug('User not logged on');
    $result = true;
  } else {
    // user logged on
    if (uga_get_option('ignore_users') && 
        $user_level>=uga_get_option('max_user_level')) {
      // ignore user because of userlevel
      uga_debug("Not tracking user with level $user_level");
      $result = false;
    } else {
      uga_debug("Tracking user with level $user_level");
      $result = true;
    }
  }
  uga_debug("Ending uga_track_user: $result");
  return $result;
}

// returns true if a URL is internal. This is the case when a URL is
// starts with any of the defined internal hostnames
// The input URL has to be stripped of any protocol:// before calling this
// function 
function uga_is_url_internal($url) {
  // check if the URL starts with any of the "internal" hostnames
  uga_debug("Start uga_is_url_internal: $url");
  $url=strtolower($url);
  $internal=false;
  $internals=explode(',', uga_get_option('internal_domains'));
  foreach ($internals as $hostname) {
    uga_debug("Checking hostname $hostname");
    $hostname=strtolower($hostname);
    if (substr($url, 0, strlen($hostname))==$hostname) {
      // URL starts with hostname of this website
      uga_debug('Match found, url is internal');
      $internal=true;
    }
  }
  uga_debug("Ending uga_is_url_internal: $internal");
  return $internal;
}

// strips the hostname from the beginning of a URL. The URL already has
// to be stripped of any "protocol://" before calling this function
function uga_remove_hostname($url) {
  // removes hostname (including first /) from URL
  // result never starts with a /
  uga_debug("Start uga_remove_hostname: $url");
  $pos=strpos($url, '/');
  $result='';
  if ($pos===false) {
    // url is only a hostname
    uga_debug('URL just hostname, return empty string');
    $result='';
  } else {
    uga_debug('Stripping everything up until and including first /');
    $result=substr($url, $pos+1);
  }
  uga_debug("Ending uga_remove_hostname: $result");
  return $result;
}

// returns the trackerString for a mailto: link
// will return an empty string when mailto: tracking is disabled
function uga_track_mailto($mailto) {
  // return tracker string for mailto: link
  uga_debug("Start uga_track_mailto: $mailto");
  $tracker='';
  if (uga_get_option('track_mail_links')) {
    $tracker=uga_get_option('prefix_mail_links').$mailto;
  }        
  uga_debug("Ending uga_track_mailto: $tracker");
  return $tracker;
}

// returns the trackerString for an internal download link
// will return an empty string if this feature is disabled
function uga_track_internal_url($url, $relative) {
  // return tracker string for internal URL
  // absolute url starts with hostname
  uga_debug("Start uga_track_internal_url: $url, $relative");
  $tracker='';
  if (uga_get_option('track_files')) {
    // check for specific file extensions on local site
    uga_debug('Tracking files enabled');
    if (strpos($url,'?') !== false) {
      // remove query parameters from URL
      $url=substr($url, 0, strpos($url, '?'));
      uga_debug("Removed query params from url: $url");
    }
    // check file extension
    $exts=explode(',', uga_get_option('track_extensions'));
    foreach ($exts as $ext) {
      uga_debug("Checking file extension $ext");
      if (substr($url, -strlen($ext)-1) == ".$ext") {
        // file extension found
        uga_debug('File extension found');
        if ($relative) {
          uga_debug('Relative URL');
          if (substr($url, 0, 1)=='/') {
            // remove starting slash from relative URL that starts from
            // root. Even better would be to rewrite relative URL that do
            // not start with a root slash, to a URL that does start from
            // root
            $url=substr($url, 1);
            uga_debug("Removed starting slash from url: $url");
          }
          $tracker=uga_get_option('prefix_file_links').$url;
        } else {
          uga_debug('Absolute URL, remove hostname from URL');
          // remove hostname from url
          $tracker=uga_get_option('prefix_file_links').uga_remove_hostname($url);
        }
      }
    }
  }
  
  uga_debug("Ending uga_track_internal_url: $tracker");
  return $tracker;

}

// returns the trackerString for an external link
// will return an empty string if this feature is disabled
function uga_track_external_url($url) {
  // return tracker string for external URL
  // url is everything after the protocol:// (e.g. www.host.com/dir/file?param)
  uga_debug("Start uga_track_external_url: $url");
  $tracker='';
  if (uga_get_option('track_ext_links')) {
    uga_debug('Tracking external links enabled');
    $tracker=uga_get_option('prefix_ext_links').$url;
  }
  uga_debug("Ending uga_track_external_url: $url");
  return $tracker;
}

// returns the trackerString for an internal/external link
// will return an empy string if tracking for this type of URL is disabled
function uga_track_full_url($url) {
  // url is everything after the protocol:// (e.g. www.host.com/dir/file?param)
  uga_debug("Start uga_track_full_url: $url");

  // check if the URL starts with any of the "internal" hostnames
  $tracker = '';
  if (uga_is_url_internal($url)) {
    uga_debug('Get tracker for internal URL');
    $tracker = uga_track_internal_url($url, false);
  } else {
    uga_debug('Get tracker for external URL');
    $tracker = uga_track_external_url($url);
  }
  uga_debug("Ending uga_track_full_url: $tracker");
  return $tracker;
}

// returns a (possibly modified) <a>...</a> link with onClick event
// added if tracking for this type of link is enabled
// this function is used as callback function in a preg_replace_callback
function uga_preg_callback($match) {
  uga_debug("Start uga_preg_callback: $match");

  // $match[0] is the complete match
  $before_href=1; // text between "<a" and "href"
  $after_href=3;  // text between the "href" attribute and the closing ">"
  $href_value=2;  // value of the href attribute
  $a_content=4;   // text between <a> and </a> tags

  $result = $match[0];
  
  // determine (if any) tracker string
  $tracker='';
  // disect target URL (1=protocol, 2=location) to determine type of URL
  if (preg_match('@^([a-z]+)://(.*)@i', trim($match[$href_value]), $target) > 0) {
    // URL with protocol and :// disected 
    uga_debug('Get tracker for full url');
    $tracker = uga_track_full_url($target[2]);
  } else if (preg_match('@^(mailto):(.*)@i', trim($match[$href_value]), $target) > 0) {
    // mailto: link found
    uga_debug('Get tracker for mailto: link');
    $tracker = uga_track_mailto($target[2]);
  } else {
    // relative URL
    uga_debug('Get tracker for relative (and thus internal) url');
    $tracker = uga_track_internal_url(trim($match[$href_value]), true);
  }

  if ($tracker) {
    // add onClick attribute to the A tag
    uga_debug("Adding onclick attribute for $tracker");
    $onClick="javascript:urchinTracker ('$tracker');";
    $result=preg_replace('@<a(.*)href@i', 
                         '<a onclick="'.$onClick.'"$1href', 
                         $result);
  }

  uga_debug("Ending uga_preg_callback: $result");
  return $result;

}

// returns true if we're currently building a feed
function uga_in_feed() {
  uga_debug('Start uga_in_feed');
  if (is_feed() || $doing_rss) {
    $result = true;
  } else {
    $result = false;
  }
  uga_debug("Ending uga_in_feed: $result");
  return $result;
}

// filter function used as filter on content and/or comments
// will add onClick tracking JavaScript to any link that required tracking
function uga_filter($content) {
  uga_debug("Start uga_filter: $content");
  if (!uga_in_feed() && uga_track_user()) {
    $pattern = '<a(.*?)href\s*=\s*[\'"](.*?)[\'"]([^>]*)>(.*?)<\s*/a\s*>';
    uga_debug("Calling preg_replace_callback: $pattern");
    $content = preg_replace_callback('@'.$pattern.'@i', 'uga_preg_callback', $content);
  }
  uga_debug("Ending uga_filter: $content");
  return $content;
}

// insert a snippet of HTML in either the header or the footer of the page
// we prefer to put this in the footer, but if the wp_footer() hook is not
// called by the template, we'll use the header
function uga_insert_html_once($location, $html) {
  uga_debug("Start uga_insert_html_once: $location, $html");
  global $uga_header_hooked;
  global $uga_footer_hooked;
  global $uga_html_inserted;
  uga_debug("Footer hooked: $uga_footer_hooked");
  uga_debug("HTML inserted: $uga_html_inserted");
  
  if ('head'==$location) {
    // header
    uga_debug('Location is HEAD');
    // notify uga_shutdown that the header hook got executed
    $uga_header_hooked = true;
    if (!uga_get_option('footer_hooked')) {
      // only insert the HTML if the footer is not hooked
      uga_debug('Inserting HTML since footer is not hooked');
      echo $html;
      $uga_html_inserted=true;
    }
  } else if ('footer'==$location) {
    // footer
    uga_debug('Location is FOOTER');
    // notify uga_shutdown that the footer hook got executed
    $uga_footer_hooked = true;
    if (!$uga_html_inserted) {
      // insert the HTML if it is not yet inserted by the HEAD filter
      uga_debug('Inserting HTML');
      echo $html;
    }
  } else if ('adm_footer'==$location) {
    // footer of admin page
    uga_debug('Location is ADM_FOOTER');
    if (!$uga_html_inserted) {
      // insert the HTML if it is not yet inserted by the HEAD filter
      uga_debug('Inserting HTML');
      echo $html;
    }
  }
  uga_debug('End uga_insert_html');
}

// return snippet of HTML to insert in the page to activate Google Analytics
function uga_get_tracker() {
  uga_debug('Start uga_get_tracker');
  $result='';
  if (!uga_in_feed() && uga_track_user()) {
    // add tracker JavaScript to the page
    $result='
<!-- tracker added by Ultimate Google Analytics plugin v1.2: http://www.oratransplant.nl/uga -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "'.uga_get_option('account_id').'";
urchinTracker();
</script>
';
  }
  uga_debug("Ending uga_get_tracker: $result");
  return $result;
}

// Hook function for wp_head action to (possibly) include the GA tracker
function uga_wp_head_track($dummy) {
  uga_debug("Start uga_wp_head_track: $dummy");
  uga_insert_html_once('head', uga_get_tracker());
  uga_debug("Ending uga_wp_head_track: $dummy");
  return $dummy;
}

// Hook function for wp_footer action to (possibly) include the GA tracker
function uga_wp_footer_track($dummy) {
  uga_debug("Start uga_wp_footer_track: $dummy");
  uga_insert_html_once('footer', uga_get_tracker());
  uga_debug("Ending uga_wp_footer_track: $dummy");
  return $dummy;
}

// Hook function for admin_footer action to (possibly) include the GA tracker
function uga_adm_footer_track($dummy) {
  uga_debug("Start uga_adm_footer_track: $dummy");
  uga_insert_html_once('adm_footer', uga_get_tracker());
  uga_debug("Ending uga_adm_footer_track: $dummy");
  return $dummy;
}

// Hook function called during shutdown (end of page)
// this determines if the wp_footer hooks executed. If not, UGA is configured
// to insert its HTML in the header and not the footer
// It also adds the debug-info as HTML comments if debugging is enabled
function uga_shutdown() {
  uga_debug('Start uga_shutdown');
  global $uga_header_hooked;
  global $uga_footer_hooked;

  if (!uga_in_feed() && uga_track_user()) {
    // we're not building a feed and this user is tracked
    if (!$uga_footer_hooked && !$uga_header_hooked) {
      // both the header and the footer hook did not execute
      // probably building some special page (e.g. wp-stattraq reports page)
      // do not change the flag to indicate whether the footer is hooked
      uga_debug('Header and footer hook were not executed');
    } else if ($uga_footer_hooked) {
      // footer hooks executed
      uga_debug('Footer hook was executed');
      if (!uga_get_option('footer_hooked')) {
        uga_debug('Changing footer_hooked option to true');
        uga_set_option('footer_hooked', true);
      }
    } else {
      // footer hook did not execute , but header hook did
      uga_debug('Footer hook was not executed, but header hook did');
      if (uga_get_option('footer_hooked')) {
        uga_debug('Changing footer_hooked option to false');
        uga_set_option('footer_hooked', false);
      }
    }
  } else {
    uga_debug('Building feed or not tracking user, not setting footer_hooked flag');
  }

  // write the debug info
  if (uga_get_option('debug')) {
    global $uga_debug;
    echo "\n<!-- \n$uga_debug -->";  
  }
  uga_debug('End uga_shutdown');
}

// **************
// initialization

uga_debug('Ultimate Google Analytics initialization');

// load texts for localization
load_plugin_textdomain('uga');

// add UGA Options page to the Option menu
add_action('admin_menu', 'uga_admin');

// add filters if enabled
if (uga_get_option('enable_tracker') && uga_get_option('filter_content')) {
  uga_debug('Adding the_content and the_excerpt filters');
  add_filter('the_content', 'uga_filter', 50);
  add_filter('the_excerpt', 'uga_filter', 50);
}
if (uga_get_option('enable_tracker') && uga_get_option('filter_comments')) {
  uga_debug('Adding comment_text filter');
  add_filter('comment_text', 'uga_filter', 50);
}
if (uga_get_option('enable_tracker') && uga_get_option('filter_comment_authors')) {
  uga_debug('Adding get_comment_author_link filter');
  add_filter('get_comment_author_link', 'uga_filter', 50);
}

// add actions if enabled
if (uga_get_option('enable_tracker')) {
  uga_debug('Adding wp_head and wp_footer action hooks for tracker');
  add_action('wp_head',   'uga_wp_head_track');
  add_action('wp_footer', 'uga_wp_footer_track');
}
if (uga_get_option('track_adm_pages')) {
  uga_debug('Adding admin_footer action hook for tracker');
  add_action('admin_footer', 'uga_adm_footer_track');
}
uga_debug('Adding shutdown action hook for debugging and notice if wp_footer is hooked');
add_action('shutdown', 'uga_shutdown');

?>