<?php
$ini_settings = $this->getIniSettings();
?>
<div class="wrap">
    <h1><?php echo __('Current PHP Settings', self::TEXT_DOMAIN); ?></h1>
    <?php require_once(CUSTOM_PHP_SETTINGS_PLUGIN_DIR . 'admin/views/cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
            <th><?php echo __('Name', self::TEXT_DOMAIN); ?></th>
            <th><?php echo __('Value', self::TEXT_DOMAIN); ?></th>
            <th><?php echo __('Default', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($ini_settings as $key => $value) : ?>
            <?php $class = ($value['global_value'] !== $value['local_value'] ? ' class="modified"' : ''); ?>
            <tr<?php echo $class; ?>>
                <td><?php echo $key; ?></td>
                <td><?php echo $value['local_value']; ?></td>
                <td><?php echo $value['global_value']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
