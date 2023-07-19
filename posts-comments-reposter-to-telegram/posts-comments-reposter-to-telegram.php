<?php
/**
 * Plugin Name:       Posts & Comments Reposter to Telegram
 * Plugin URI:        https://wprepostertg.wordpress.com
 * Description:       Automatic publication of new published posts and approved comments in Telegram channels
 * Version:           1.0
 * Requires at least: 3.8
 * Requires PHP:      5.6
 * Author:            Ivan Matveev
 * Author URI:        https://github.com/ivanmtw
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       posts-comments-reposter-to-telegram
 */

defined('ABSPATH') || exit;

function wp_reposter_tg_load_textdomain()
{
    load_plugin_textdomain('posts-comments-reposter-to-telegram', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'wp_reposter_tg_load_textdomain');

function wp_reposter_tg_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=posts-comments-reposter-to-telegram">' . __('Settings', 'posts-comments-reposter-to-telegram') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_reposter_tg_settings_link');

function wp_reposter_tg_settings_page()
{
    add_submenu_page(
        'options-general.php',
        __('Reposter To Telegram', 'posts-comments-reposter-to-telegram'),
        __('Reposter To Telegram', 'posts-comments-reposter-to-telegram'),
        'manage_options',
        'posts-comments-reposter-to-telegram',
        'wp_reposter_tg_settings_page_content'
    );
}
add_action('admin_menu', 'wp_reposter_tg_settings_page');

function wp_reposter_tg_settings_page_content()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_reposter_tg_posts_settings');
            do_settings_sections('wp_reposter_tg_posts_settings');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

function wp_reposter_tg_register_settings()
{
    add_settings_section(
        'wp_reposter_tg_posts_settings_section',
        __('Sending Posts and Approved Comments to Telegram', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_posts_settings_section_callback',
        'wp_reposter_tg_posts_settings',
    );

    add_settings_field(
        'wp_reposter_tg_posts_enabled',
        __('Enable Automatic Sending of Published Posts to Telegram', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_posts_enabled_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    add_settings_field(
        'wp_reposter_tg_posts_channel_chat_id',
        __('Telegram Channel Chat_ID', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_posts_channel_chat_id_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    add_settings_field(
        'wp_reposter_tg_posts_bot_token',
        __('Bot Token', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_posts_bot_token_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    add_settings_field(
        'wp_reposter_tg_comments_enabled',
        __('Enable Automatic Sending of Approved Comments to Telegram', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_comments_enabled_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    add_settings_field(
        'wp_reposter_tg_comments_channel_chat_id',
        __('Telegram Channel Chat_ID', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_comments_channel_chat_id_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    add_settings_field(
        'wp_reposter_tg_comments_bot_token',
        __('Bot Token', 'posts-comments-reposter-to-telegram'),
        'wp_reposter_tg_comments_bot_token_callback',
        'wp_reposter_tg_posts_settings',
        'wp_reposter_tg_posts_settings_section',
    );

    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_posts_enabled');
    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_posts_channel_chat_id');
    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_posts_bot_token');
    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_comments_enabled');
    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_comments_channel_chat_id');
    register_setting('wp_reposter_tg_posts_settings', 'wp_reposter_tg_comments_bot_token');
}
add_action('admin_init', 'wp_reposter_tg_register_settings');

function wp_reposter_tg_posts_settings_section_callback()
{
    echo '<p>' . __('Specify the Settings for Posting Published Posts and Approved Comments in the Telegram Channel', 'posts-comments-reposter-to-telegram') . '</p>';
}

function wp_reposter_tg_posts_enabled_callback()
{
    $checked = get_option('wp_reposter_tg_posts_enabled', false);
    echo '<input type="checkbox" name="wp_reposter_tg_posts_enabled" value="1" ' . checked($checked, true, false) . ' />';
}

function wp_reposter_tg_posts_channel_chat_id_callback()
{
    $channel_chat_id = get_option('wp_reposter_tg_posts_channel_chat_id');
    printf(
        '<input type="text" name="wp_reposter_tg_posts_channel_chat_id" value="%s" size="50" /><p class="description">%s</p><p class="description">%s</p><p class="description">%s</p><p class="description">%s</p>',
        esc_attr($channel_chat_id),
        __('Specify the chat ID, its name, or a link', 'posts-comments-reposter-to-telegram'),
        __('To Publish to a Public Channel, Specify Its Name or Link (for Example, @durov or https://t.me/durov)', 'posts-comments-reposter-to-telegram'),
        __('To Publish to a Private Channel, Specify a Link to Any Post in the Channel (for Example https://t.me/c/1234567890/1)', 'posts-comments-reposter-to-telegram'),
        __('If There Are No Posts, Then Write a Test Post and Copy the Link to This Field (This Is Necessary to Get the Channel Chat ID)', 'posts-comments-reposter-to-telegram'),
    );
}

function wp_reposter_tg_posts_bot_token_callback()
{
    $bot_token = get_option('wp_reposter_tg_posts_bot_token');
    printf(
        '<input type="text" name="wp_reposter_tg_posts_bot_token" value="%s" size="50" /><p class="description">%s</p><p class="description">%s</p>',
        esc_attr($bot_token),
        __('Specify the Token of the Bot That Will Send Published Posts to Telegram', 'posts-comments-reposter-to-telegram'),
        __('To Get a Token, Create a Bot Using the User <a href="https://t.me/BotFather " target="_blank">@BotFather</a>', 'posts-comments-reposter-to-telegram'),
    );
}

function wp_reposter_tg_comments_enabled_callback()
{
    $checked = get_option('wp_reposter_tg_comments_enabled', false);
    echo '<input type="checkbox" name="wp_reposter_tg_comments_enabled" value="1" ' . checked($checked, true, false) . ' />';
}

function wp_reposter_tg_comments_channel_chat_id_callback()
{
    $channel_chat_id = get_option('wp_reposter_tg_comments_channel_chat_id');
    printf(
        '<input type="text" name="wp_reposter_tg_comments_channel_chat_id" value="%s" size="50" /><p class="description">%s</p><p class="description">%s</p><p class="description">%s</p><p class="description">%s</p>',
        esc_attr($channel_chat_id),
        __('Specify the chat ID, its name, or a link', 'posts-comments-reposter-to-telegram'),
        __('To Publish to a Public Channel, Specify Its Name or Link (for Example, @durov or https://t.me/durov)', 'posts-comments-reposter-to-telegram'),
        __('To Publish to a Private Channel, Specify a Link to Any Post in the Channel (for Example https://t.me/c/1234567890/1)', 'posts-comments-reposter-to-telegram'),
        __('If There Are No Posts, Then Write a Test Post and Copy the Link to This Field (This Is Necessary to Get the Channel Chat ID)', 'posts-comments-reposter-to-telegram'),
    );
}

function wp_reposter_tg_comments_bot_token_callback()
{
    $bot_token = get_option('wp_reposter_tg_comments_bot_token');
    printf(
        '<input type="text" name="wp_reposter_tg_comments_bot_token" value="%s" size="50" /><p class="description">%s</p><p class="description">%s</p>',
        esc_attr($bot_token),
        __('Specify the Token of the Bot That Will Send Approved Comments to Telegram', 'posts-comments-reposter-to-telegram'),
        __('To Get a Token, Create a Bot Using the User <a href="https://t.me/BotFather " target="_blank">@BotFather</a>', 'posts-comments-reposter-to-telegram'),
    );
}

function wp_reposter_tg_save_enabled($value)
{
    return isset($value) ? true : false;
}
add_filter('sanitize_option_wp_reposter_tg_posts_enabled', 'wp_reposter_tg_save_enabled');
add_filter('sanitize_option_wp_reposter_tg_comments_enabled', 'wp_reposter_tg_save_enabled');

function wp_reposter_tg_save_channel_chat_id($value)
{
    if (strlen($value)) {
        $pattern = '/\/(\d+)\//';
        preg_match($pattern, $value, $matches);
        if (isset($matches[1])) {
            $value = '-100' . $matches[1];
        } else {
            $pattern = '/https:\/\/t.me\/([a-zA-Z0-9_]+)\//';
            preg_match($pattern, $value, $matches);
            if (isset($matches[1])) {
                $value = $matches[1];
            } else {
                $value = str_replace(['https://t.me/', '@'], '', $value);
            }
        }
    }
    return $value;
}
add_filter('sanitize_option_wp_reposter_tg_posts_channel_chat_id', 'wp_reposter_tg_save_channel_chat_id');
add_filter('sanitize_option_wp_reposter_tg_comments_channel_chat_id', 'wp_reposter_tg_save_channel_chat_id');

function wp_reposter_tg_publish_to_telegram($new_status, $old_status, $post_or_comment_object)
{
    $message = '';
    $checked = false;

    $arr = array(
        'a' => array(
            'href' => array()
        ),
        'b' => array(),
        'code' => array(),
        'del' => array(),
        'em' => array(),
        'i' => array(),
        'ins' => array(),
        'pre' => array(),
        's' => array(),
        'span' => array(),
        'strike' => array(),
        'strong' => array(),
        'tg-spoiler' => array(),
        'u' => array()
    );

    if (get_class($post_or_comment_object) == 'WP_Comment') {

        $checked = get_option('wp_reposter_tg_comments_enabled', false);
        if (!$checked) {
            return;
        }

        if ($new_status === 'approved' && $old_status !== 'approved') {

            $chat_id = get_option('wp_reposter_tg_comments_channel_chat_id');
            $bot_token = get_option('wp_reposter_tg_comments_bot_token');

            if (!$chat_id or !$bot_token) {
                return;
            }

            $comment_url = get_comment_link($post_or_comment_object->comment_ID);
            $comment_content = wp_kses(trim(get_comment_excerpt($post_or_comment_object->comment_ID)), $arr) . '...';

            $message = sprintf(
                "<b>Новый комментарий</b>\n\n%s\n\n<a href=\"%s\">%s</a>\n",
                $comment_content,
                $comment_url,
                $comment_url,
            );
        }
    }

    if (get_class($post_or_comment_object) == 'WP_Post') {

        $checked = get_option('wp_reposter_tg_posts_enabled', false);
        if (!$checked) {
            return;
        }

        if ($new_status === 'publish' && $old_status !== 'publish') {

            $chat_id = get_option('wp_reposter_tg_posts_channel_chat_id');
            $bot_token = get_option('wp_reposter_tg_posts_bot_token');

            if (!$chat_id or !$bot_token) {
                return;
            }

            $post_url = get_permalink($post_or_comment_object->ID);
            $post_title = get_the_title($post_or_comment_object->ID);
            $post_content = wp_kses(trim(get_the_excerpt($post_or_comment_object->ID)), $arr) . '...';

            $message = sprintf(
                "<b>%s</b>\n\n%s\n\n<a href=\"%s\">%s</a>\n",
                $post_title,
                $post_content,
                $post_url,
                $post_url,
            );
        }
    }

    if ($message) {
        $url = 'https://api.telegram.org/bot' . $bot_token . '/sendMessage';
        $data = array(
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'html'
        );
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    }
}
add_action('transition_post_status', 'wp_reposter_tg_publish_to_telegram', 10, 3);
add_action('transition_comment_status', 'wp_reposter_tg_publish_to_telegram', 10, 3);
