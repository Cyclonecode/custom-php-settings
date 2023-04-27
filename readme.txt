=== Custom PHP Settings ===
Contributors: cyclonecode
Donate link: https://www.buymeacoffee.com/cyclonecode
Tags: php, htaccess, settings, apache, apache2
Requires at least: 3.1.0
Tested up to: 6.2
Requires PHP: 5.6
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes it possible to override php settings.

== Description ==

This plugin can be used to customize php settings for you Wordpress installation.

The plugin will modify either the **.htaccess** file or **.user.ini** file in order to change the current php settings directly from within the settings page.

Since the configuration file needs to be modified this file **must** be writable for this plugin to work as expected.

= Looking for help =

I am currently in the search for someone who would like to help me with something of the following:

- Create a dashboard icon which can be used in the admin menu.
- Create a banner that would be displayed on the plugins homepage at wordpress.org.
- Design a nicer and more intuitive admin interface.
- Create a solid looking icon that can be used on multiple places.

If you would like to help with anything of the above, please do not hesitate and contact me either on slack or by email.

= Pro version =

The pro version comes with extended features such as:

- Support to set environment variables in your .htaccess file.
- Support to enable/disable WP_DEBUG from the configuration page.
- Enable error logging and set path to log file.
- Hints for most of the configurable PHP settings.
- Support to backup your configuration file before applying any changes.
- Create multiple configurations that can be used to easily switch between different settings.
- Extended support.

You can get the premium version for only 15 EUR, or by buying me a couple of beers at [buymeacoffee](https://www.buymeacoffee.com/cyclonecode).
Do not forget to add your email address or other contact information, and I will send you a reply with instructions on how to get the premium version.

The Pro license is valid for a year and can be used on up to three sites.

Please contact me by e-mail at cyclonecode@gmail.com for further instructions on how to get the pro version.

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

Some settings might be displayed in red because they are changed somewhere else, perhaps through a customized php.ini file, by Wordpress itself, a plugin or in some other way.
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

= I have saved a setting in the editor but the PHP setting does not reflect this? =
Make sure so you have checked the *Update configuration file* checkbox beneath the editor.

== Support ==

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/custom-php-settings/](https://wordpress.org/support/plugin/custom-php-settings/)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

== Installation ==

1. Upload custom-php-settings to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.
3. You can now modify your php settings by going to the settings page located under *wp-admin/tools.php?page=custom-php-settings*.

== Upgrade Notice ==

= 1.1.0 =
Check Server API and do **not** make any changes if using CGI/Fast-CGI.

= 1.2.6 =
Fixes a bug where the plugin could not be deleted.

= 1.4.3
- Fixes a bug on status page when no custom settings has been added.

== Screenshots ==

1. Customize PHP settings from within Wordpress administration.
2. A `.htaccess` file with customized PHP settings.
3. A table with all php settings that can be customized.
4. Table displaying information about the current php environment.
5. Listing of all enabled PHP extensions.
6. All $_SERVER variables.
7. All $_COOKIE variables.

== Changelog ==

= dev
- Fix faulty clean() method in settings.
