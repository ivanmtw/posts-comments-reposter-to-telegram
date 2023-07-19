# Posts & Comments Reposter to Telegram
Contributors: (this should be a list of wordpress.org userid's)
Tags: posts, comments, telegram, channel
Requires at least: 3.8
Tested up to: 3.8
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatic publication of new published posts and approved comments in Telegram channels

## Description

Our plugin allows you to send published posts and approved comments from your website to Telegram channels. 

With the help of the plugin, you can send content to private or public Telegram channels. You can choose whether to 
send all posts or comments to one or more channels. To use the plugin, you need to create channels and bots in the 
official Telegram application. After activating the plugin on your website, you will be able to configure it through 
the Settings page, where you need to specify channel IDs and bot tokens to send. As soon as you publish a post or 
approve a comment, the information will be automatically sent to the specified Telegram channels. 

Our plugin provides a simple and effective way to promote your content on the Telegram platform and make it available 
to a large audience.

## Installation

This section describes how to install the plugin and get it working.

e.g.

1. Upload `posts-comments-reposter-to-telegram.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Fill Chat_ID and Bot Tokens in Settings page `/wp-admin/options-general.php?page=posts-comments-reposter-to-telegram`

== Frequently Asked Questions ==

## How to get the Chat_ID of the Telegram channel?

1. Chat_ID for a public channel is its name, f.e. @durov или https://t.me/durov
2. To get the Chat_ID of a private channel, create any post in the channel and copy the link to it. The plugin itself 
gets the chat ID from this link.

## How to get a bot token?

to get a bot token, you need to create a bot in the Telegram application using @BotFather. Find this user via Telegram 
search, then follow his instructions. After creating a bot, you will be given its Bot Token.
