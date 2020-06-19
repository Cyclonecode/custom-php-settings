<div class="wrap">
    <h1><?php echo __('COOKIE Variables', self::TEXT_DOMAIN); ?></h1>
    <?php require_once(CUSTOM_PHP_SETTINGS_PLUGIN_DIR . 'admin/views/cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
        <th><?php echo __('Name', self::TEXT_DOMAIN); ?></th>
        <th><?php echo __('Value', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($_COOKIE as $name => $value) : ?>
            <tr>

                <td><?php echo esc_html($name); ?></td>
                <td><?php echo esc_html($value); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>