OK...

Just drop the plugin file ('spam-karma.php') inside "wp-content/plugins" and the rest in a folder called "spam-karma" that you create inside "wp-content".

Make sure "captcha_temp" is writable by the server (chmod 0777).

Plugin will work straight off the box (once activated), but to use the blacklist feature, you need to log at least once and go to the options page: Options -> Spam Karma

Important: if you are using Spam Karma with Wordpress 1.2, you cannot use the standard configuration link. You need to browse directly to the following URL (provided as a reminder in the plugin description on the Plugin admin screen): http://[yourdomain.com]/[yourblog]/wp-content/plugins/spam-karma.php?spamk_setup


Please report any bug or bad detection...