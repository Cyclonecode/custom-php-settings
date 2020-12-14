<?php




$settings = array(
    __('Version', self::TEXT_DOMAIN) => apache_get_version(),
    __('Loaded Modules', self::TEXT_DOMAIN) => implode(', ', apache_get_modules()),
);
apache_get_modules();
?>
<div class="wrap">
    <h1><?php echo __('Apache Information', self::TEXT_DOMAIN); ?></h1>
    <?php require_once('cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
        <th><?php echo __('Name', self::TEXT_DOMAIN); ?></th>
        <th><?php echo __('Value', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($settings as $key => $value) : ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $value; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
