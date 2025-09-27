<?php
$settings = $module->settings;

$title = $settings->title ?? 'Share this with your friends';
$networks = $settings->networks ?? ['facebook', 'twitter', 'linkedin'];
$iconset = $settings->iconset ?? 'default_square';
$alignment = $settings->alignment ?? 'left';

// Ensure networks is an array
if (!is_array($networks)) {
    $networks = [$networks];
}

// Set iconset on renderer
if (method_exists($module->shareRenderer, 'setIconset')) {
    $module->shareRenderer->setIconset($iconset);
}
?>

<div class="fl-html-social-share-buttons fl-module" style="text-align: <?php echo esc_attr($alignment); ?>;">
    <?php if (!empty($title)) : ?>
        <div class="share-title"><?php echo esc_html($title); ?></div>
    <?php endif; ?>

    <div class="share-buttons">
        <?php foreach ($networks as $network) : ?>
            <?php
            $profile = ['handle' => '@example', 'network' => $network];
            $buttonHtml = $module->shareRenderer->render($network, $profile);
            echo $buttonHtml . ' ';
            ?>
        <?php endforeach; ?>
    </div>
</div>