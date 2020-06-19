=== Custom PHP Settings ===
Contributors: cyclonecode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PBTHN3L67QA2S&source=url&lc=US&item_name=Custom+PHP+Settings
Tags: php, htaccess, settings, apache, apache2
Requires at least: 3.1.0
Tested up to: 5.4.2
Requires PHP: 5.4
Stable tag: 1.2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes it possible to override php settings.

== Description ==

This plugin can be used to customize php settings for you wordpress installation.

The plugin will modify either the **.htaccess** file or **.user.ini** file in order to change the current php settings directly from within the settings page.

Since the configuration file needs to be modified this file **must** be writable for this plugin to work as expected.

= Apache module =

When PHP is running as an Apache module the **.htaccess** file will be used to set customized settings; make sure so that this file **exists** and is **writable** by the webserver.

= CGI/Fast-CGI =

If instead PHP is running in CGI/Fast-CGI mode then a custom INI file will be used. The name of this file depends on the value of **user_ini.filename** in the php configuration, by default it is **.user.ini**.
You can check the name of you custom INI file in the **PHP Information** table. The custom INI file should be placed under the root folder and **most** be **writable** by the webserver.

Notice that there is also a **User INI file cache TTL** value in the information table, this value tells how long the custom INI file will be cached before it gets reloaded.
For instance, if this value is set to 300 then any changes to your custom INI file will not be reflected for up to 5 minutes. The name for this setting in the php configuration is **user_ini.cache_ttl**.

On important thing is to make sure that your `.user.ini` file is blocked by your webserver. If you are running NGINX this can be done by adding:

`
location ~ /\.user\.ini {
  deny all;
}
`

to your server configuration. The same thing using Apache is done by adding the following to the configuration if not already done:

`
<Files .user.ini>
order allow,deny
deny from all
</Files>
`

= Available Settings =

The settings table will display all non-system php settings that can be customized by the plugin. All modified settings will be displayed in red in this table.

Some settings might be displayed in red because they are changed somewhere else, perhaps through a customized php.ini file, by wordpress itself, a plugin or in some other way.
For instance if you have enabled **WP_DEBUG** in your **wp-config.php** file the **error_reporting** setting will turn red.

If you have questions or perhaps some idea on things that should be added you can also try [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

= Resources =

A complete list of settings that can be modified can be found here: [List of php.ini directives](http://php.net/manual/en/ini.list.php)
Notice that directives marked as `PHP_INI_SYSTEM` can not be modified.

== Warning ==

Make sure you know how a value should be configured and what different settings do before changing anything.
This is important since some settings might render your page inaccessible, depending on what value you are using.
A good example of this is the **variables_order** configuration:

> Sets the order of the EGPCS (Environment, Get, Post, Cookie, and Server) variable parsing. For example, if variables_order is set to "SP" then PHP will create the superglobals $_SERVER and $_POST, but not create $_ENV, $_GET, and $_COOKIE. Setting to "" means no superglobals will be set.

If this value would be configured to **EPCS** then no **$_GET** superglobal would be set which would make your page inaccessible.

Another example is setting the **post_max_size** to a very low value so that no form data is sent to the server, which in turn would result in that form data is never saved.

If you by mistake changed some value and your site is now inaccessible you could simply manually remove everything from between the plugin markers in your **.htaccess** file:

`
# BEGIN CUSTOM PHP SETTINGS
php_value variables_order EPCS  <-- Remove
# END CUSTOM PHP SETTINGS
`

== Frequently Asked Questions ==

== Support ==

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/custom-php-settings/](https://wordpress.org/support/plugin/custom-php-settings/)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

== Installation ==

1. Upload custom-php-settings to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.
3. You can now modify your php settings by going to the settings page located under *wp-admin/tools.php?page=custom-php-settings*.

== Frequently Asked Questions ==

== Upgrade Notice ==

= 1.1.0 =
Check Server API and do **not** make any changes if using CGI/Fast-CGI.

= 1.2.6 =
Fixes a bug where the plugin could not be deleted.

== Screenshots ==

1. Customize PHP settings from within wordpress administration.
2. A `.htaccess` file with customized PHP settings.
3. A table with all php settings that can be customized.
4. Table displaying information about the current php environment.
5. Listing of all enabled PHP extensions.
6. All $_SERVER variables.
7. All $_COOKIE variables.

== Changelog ==

= 1.2.6 =
- Fixes a bug where the plugin could not be deleted.

= 1.2.5 =
- Add correct comment character when using `.user.ini` file.
- Move `.user.ini` to root instead of the `wp-admin` folder, so settings have a global effect.
- Tweak file permission for both `.user.ini` and `.htaccess` file.

= 1.2.4 =
- Escape data.
- Add tab support for all sections.
- Only show sub section if not empty.
- Check if settings needs to be updated.

= 1.2.3 =
- Fix permission check for configuration file.

= 1.2.2 =
- Add global value to settings table.
- Add dynamic tabs for super globals.
- Add footer text in admin dashboard.

= 1.2.1 =
- Check if wp_enqueue_code_editor() does exist.
- Use array instead of string in call to insert_with_markers().
- Switch to shell mode in codemirror.
- Get name and use configured user INI file.
- Add information about user INI file.
- Added composer support.

= 1.2.0 =
- Refactor code to use templates.
- Use .user.ini or .htaccess file depending on server api.
- Add more PHP information.
- Fix misspelled class name.

= 1.1.0 =
- Add deactivation hook.
- Add option to restore the .htaccess file when the plugin is deactivated ors uninstalled.
- Make sure no changes is done in CGI/Fast-CGI mode.
- Add basic PHP information table.

= 1.0.1 =
- Add class constants.
- Add settings table.

= 1.0.0 =
- Initial commit.
