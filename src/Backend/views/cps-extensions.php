<?php
$extensions = get_loaded_extensions();
natcasesort($extensions);
foreach ($extensions as $key => $extension) {
    $version = phpversion($extension);
    $extensions[$key] = array(
            'version' => $version,
            'name' => $extension,
    );
}
?>
<div class="wrap">
    <h1><?php echo __('Loaded Extensions', self::TEXT_DOMAIN); ?></h1>
    <?php require_once('cps-tabs.php'); ?>
    <?php settings_errors(); ?>
    <table class="custom-php-settings-table widefat">
        <thead>
            <th><?php echo __('Extension', self::TEXT_DOMAIN); ?></th>
            <th><?php echo __('Version', self::TEXT_DOMAIN); ?></th>
        </thead>
        <?php foreach ($extensions as $key => $extension) : ?>
        <tr>
            <td><?php echo $extension['name']; ?></td>
            <td><?php echo $extension['version']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
