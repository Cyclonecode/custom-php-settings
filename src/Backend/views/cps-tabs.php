<?php
$variables_order = ini_get('variables_order');
$tabs = array(
    'general' => array(
        'label' => __('Editor', self::TEXT_DOMAIN),
    ),
    'settings' => array(
        'label' => __('Settings', self::TEXT_DOMAIN),
    ),
    'info' => array(
        'label' => __('PHP Information', self::TEXT_DOMAIN),
        'children' => array(
            'php-info' => array(
                'label' => __('PHP', self::TEXT_DOMAIN),
            ),
            'extensions' => array(
                'label' => __('Extensions', self::TEXT_DOMAIN),
            )
        ),
    ),
);
if (strstr(php_sapi_name(), 'apache')) {
    $tabs['apache'] = array(
        'label' => __('Apache', self::TEXT_DOMAIN),
    );
}
if (strchr($variables_order, 'C') && !empty($_COOKIE)) {
    $tabs['info']['children']['cookie-vars'] = array(
        'label' => __('COOKIE', self::TEXT_DOMAIN),
    );
}
if (strchr($variables_order, 'S') && !empty($_SERVER)) {
    $tabs['info']['children']['server-vars'] = array(
        'label' => __('SERVER', self::TEXT_DOMAIN),
    );
}
if (strchr($variables_order, 'E') && !empty($_ENV)) {
    $tabs['info']['children']['env-vars'] = array(
        'label' => __('ENV', self::TEXT_DOMAIN),
    );
}
$tabs['status'] = array(
  'label' => __('Status', self::TEXT_DOMAIN),
);
?>
<h2 class="nav-tab-wrapper">
    <?php foreach ($tabs as $key => $item) : ?>
        <?php $active = ($key === $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <a class="nav-tab<?php echo $active; ?>"
           href="<?php echo admin_url('tools.php?page=custom-php-settings&tab=' . $key); ?>"><?php echo $item['label']; ?></a>
    <?php endforeach; ?>
    <?php foreach ($tabs as $key => $item) : ?>
        <?php $active = ($key === $this->getCurrentTab() ? ' nav-tab-active' : ''); ?>
        <?php if ($active && isset($item['children'])) : ?>
            <h3 class="nav-tab-wrapper">
                <?php foreach ($item['children'] as $subKey => $subItem) : ?>
                    <?php $active = ($subKey === $this->getCurrentSection() ? ' nav-tab-active' : ''); ?>
                    <a class="nav-tab nav-tab-small<?php echo $active; ?>"
                       href="<?php echo admin_url('tools.php?page=custom-php-settings&tab=' . $key . '&section=' . $subKey); ?>">
                        <?php echo $subItem['label']; ?>
                    </a>
                <?php endforeach; ?>
            </h3>
        <?php endif; ?>
    <?php endforeach; ?>
</h2>
