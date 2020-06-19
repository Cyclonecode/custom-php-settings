<?php
$extensions = get_loaded_extensions();
natcasesort($extensions);
?>

<div class="wrap">
    <h1><?php echo __('Loaded Extensions', self::TEXT_DOMAIN); ?></h1>
    <?php require_once(CUSTOM_PHP_SETTINGS_PLUGIN_DIR . 'admin/views/cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
            <th><?php echo __('Extension', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($extensions as $key => $extension) : ?>
        <tr>
            <td><?php echo $extension; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>