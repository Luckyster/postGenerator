<?php
namespace PostGenerator;

class SettingsPage {
    public static function addPluginMenu() {
        add_menu_page(
            'Post Generator',
            'Post Generator',
            'manage_options',
            'post_generator_generator',
            [self::class, 'renderGeneratorTab']
        );
        add_submenu_page(
            'post_generator_generator',
            'Generator',
            'Generator',
            'manage_options',
            'post_generator_generator',
            [self::class, 'renderGeneratorTab']
        );
        add_submenu_page(
            'post_generator_generator',
            'Settings',
            'Settings',
            'manage_options',
            'post_generator_settings',
            [self::class, 'renderSettingsTab']
        );

    }

    public static function registerSettings() {
        register_setting('post_generator_settings_group', 'post_generator_api_key');
    }

    public static function renderSettingsTab() {
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('post_generator_settings_group');
                do_settings_sections('post_generator_settings');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="post_generator_api_key" value="<?php echo esc_attr(get_option('post_generator_api_key')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function renderGeneratorTab() {
        ?>
        <div class="wrap">
            <h1>Post Generator</h1>
            <div id="prompt-container" class="prompt-container">
                <input type="text" id="user-prompt" placeholder="Enter a topic..." class="user-prompt"/>
                <button id="submit-prompt" class="submit-prompt">Submit</button>
                <div id="loading-spinner" class="loading-spinner">
                    <img src="<?php echo plugin_dir_url(__DIR__) . 'assets/img/loader.gif'; ?>" alt="Loading..." width="30" height="30"/>
                </div>
            </div>
            <div id="response-message" class="response-message"></div>
        </div>
        <?php
    }
}
