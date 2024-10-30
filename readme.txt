=== Monitor.chat - Monitor WordPress with Instant Messages ===
Contributors: Edward Stoever
Donate link: https://e2e.ee/en/inf/don/
Tags: monitor, chat, monitoring, xmpp, uptime, events, notify, akismet, updraft, woocommerce, statistics
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 1.1.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Your WordPress server will keep you informed in real-time with instant messages that you receive on your mobile device.

== Description ==

Imagine opening your mobile device and immediately viewing messages sent from your WordPress server with important information about what is happening right now. Stay informed on uptime, downtime, backups, memory usage, disk usage, intrusion attempts, sign-ups, or anything that matters to you!

With the Monitor.chat plugin, you will stay informed about:

* Logins, including failed login attempts
* New posts
* New comments
* Software updates including core, plugins, and themes
* File system space available
* Server memory usage

The Monitor.chat plugin will notify you by instant message on events and scheduled reports from the following plugins:

* Akismet Anti-Spam
* Updraft Backups
* Gwolle Guestbook
* Woocommerce
* WP-Statistics 

The Monitor.chat plugin relies on an external service. Messages are posted by the Monitor.chat plugin along with an API key to an external server. The external server converts the API key into the correct recipient(s) and sends the message as an XMPP instant message.
* Website: https://monitor.chat
* Policies of Monitor.chat: https://monitor.chat/documentation/policies/ 

== Installation ==

Upload the Monitor.chat plugin to your WordPress installation, activate it, and then enter your Monitor.chat API key.

== Frequently Asked Questions ==

= How does Monitor.chat plugin work? =

An event occurs on your WordPress website such as a login. This event posts a message to the Monitor.chat server using curl. The post is converted into a text message that is delivered to your mobile device. Messages can be scrolled through at any time to keep you informed about activity on your website.

= Will sending messages slow down my website? =

Messages are sent in a backgroud process that will not hinder the responsiveness of your WordPress website.

== Screenshots ==

1. The Monitor.chat administration about screen.
2. The Monitor.chat administration API Key screen.
3. Example 1 of mobile device receiving messages from WordPress.
4. Example 2 of mobile device receiving messages from WordPress.
5. Example 3 of mobile device receiving messages from WordPress.

== Changelog ==
= 1.1.1 =
* Session variables only for administrators (can activate plugins)
* Currency symbol added to messages regarding Woocommerce orders
* Profile update message is no longer triggered for new accounts

= 1.1.0 =
* Hostname is now a value stored as a setting and can be edited
* API Key Report is now visible in a modal window

= 1.0.3 =
* removing mb_strtolower, it is unnecessary and not installed in all php environments

= 1.0.2 =
* Text domain bug fixed, changed monitorchat.php to monitor-chat.php

= 1.0.1 =
* Code cleanup, revision 1 per WordPress guidelines
* Added PII masking. An email such as someone@domain.net can be presented as s*****e@d****n.net
* Form validation on API Key tab
* Replaced PHP Curl with WordPress Remote Post API

= 1.0.0 =
* Release Date 30 January 2021
* Initial release with 29 notifications by instant message included

== Upgrade Notice ==

- None

