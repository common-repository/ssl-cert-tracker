=== SSL Cert Tracker ===
Contributors: Damon Warren
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=V6V34TB7BGTRC&lc=US&item_name=Damon%20Warren&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: ssl, certificate, management, widget
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.0.2

A widget displaying SSL certificate expiration dates.

== Description ==

This is a simple widget style plugin to track SSL Certificate expirations.
It will aquire the expiration date automatically when you enter a host name.

For now, version 1.0 is very limited. I'd like to add the following features
to future releases.

1. Allow manual date entry (for tracking non-public certs)
2. Allow port specification for non-standard ports
3. Allow other types of certificates to be tracked (email, code signing, etc)

== Installation ==

1. Upload `ssl-cert-tracker` folder to the `/wp-content/plugins/` directory
2. Activate SSL Cert Tracker through the 'Plugins' menu in WordPress
3. Add the widget to a sidebar through the 'Widgets' menu in WordPress optionally set a title
4. Add hosts you wish to track through the 'SSL Cert Tracker' settings page
5. Done!

== Screenshots ==

1. SSL Cert Tracker settings page

== Change Log ==

= 1.0.2 =

Fixed a bug where users would see warnings when adding an invalid host.

= 1.0.1 =

Fixed a fatal error in widget display.


== Upgrade Notice ==

= 1.0.2 =
Fixed a bug where users would see warnings when adding an invalid host.

= 1.0.1 =
Fixed a fatal error in widget display.