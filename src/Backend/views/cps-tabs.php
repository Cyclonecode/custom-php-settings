<?php
$tabs = array(
    'general' => __('Editor', self::TEXT_DOMAIN),
    'settings' => __('Settings', self::TEXT_DOMAIN),
    'info' => __('PHP Information', self::TEXT_DOMAIN),
);
$variables_order = ini_get('variables_order');
$childTabs = array(
    'php-info' => __('PHP', self::TEXT_DOMAIN),
    'extensions' => __('Extensions', self::TEXT_DOMAIN),
);
if (strstr(php_sapi_name(), 'apache')) {
    $tabs['apache'] = __('Apache', self::TEXT_DOMAIN);
}
if (strchr($variables_order, 'C') && !empty($_COOKIE)) {
    $childTabs['cookie-vars'] = __('COOKIE', self::TEXT_DOMAIN);
}
if (strchr($variables_order, 'S') && !empty($_SERVER)) {
    $childTabs['server-vars'] = __('SERVER', self::TEXT_DOMAIN);
}
if (strchr($variables_order, 'E') && !empty($_ENV)) {
    $childTabs['env-vars'] = __('ENV', self::TEXT_DOMAIN);
}
?>
<h2 class="nav-tab-wrapper">
    <?php foreach ($tabs as $key => $label) : ?>
        <?php $active = ($key === $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <a class="nav-tab<?php echo $active; ?>"
           href="<?php echo admin_url('tools.php?page=custom-php-settings&tab=' . $key); ?>"><?php echo $label; ?></a>
    <?php endforeach; ?>
    <?php foreach ($tabs as $key => $label) : ?>
        <?php $active = ($key === $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <?php if ($active && $this->getCurrentTab() === 'info') : ?>
            <h3 class="nav-tab-wrapper">
                <?php foreach ($childTabs as $subKey => $subLabel) : ?>
                    <?php $active = ($subKey === $this->getCurrentSection() ? ' nav-tab-active' : ''); ?>
                    <a class="nav-tab nav-tab-small<?php echo $active; ?>"
                       href="<?php echo admin_url('tools.php?page=custom-php-settings&tab=' . $key . '&section=' . $subKey); ?>">
                        <?php echo $subLabel; ?>
                    </a>
                <?php endforeach; ?>
            </h3>
        <?php endif; ?>
    <?php endforeach; ?>
</h2>
