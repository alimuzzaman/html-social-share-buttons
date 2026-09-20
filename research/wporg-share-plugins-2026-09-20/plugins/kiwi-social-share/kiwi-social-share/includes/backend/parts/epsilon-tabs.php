<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="epsilon-ui-tabs">
	<ul>
		<li class="<?php echo ( $hash === 'sl-kiwi-tab-networks' || empty( $hash ) ) ? 'selected' : ''; ?>">
			<a href="#" data-tab="sl-kiwi-tab-networks"><i class="dashicons dashicons-laptop"></i>
				<?php echo esc_html__( 'Networks & Colors', 'kiwi-social-share' ); ?>
			</a>
		</li>
		<li class="<?php echo ( $hash === 'sl-kiwi-tab-article-bar' ) ? 'selected' : ''; ?>">
			<a href="#" data-tab="sl-kiwi-tab-article-bar"><i class="dashicons dashicons-editor-insertmore"></i>
				<?php echo esc_html__( 'Article bar', 'kiwi-social-share' ); ?>
			</a>
		</li>
        <li class="<?php echo ( $hash === 'sl-kiwi-tab-floating-bar' ) ? 'selected' : ''; ?>">
            <a href="#" data-tab="sl-kiwi-tab-floating-bar"><i class="dashicons dashicons-align-left"></i>
				<?php echo esc_html__( 'Floating bar', 'kiwi-social-share' ); ?>
            </a>
        </li>
		<li class="<?php echo ( $hash === 'sl-kiwi-tab-social-identity' ) ? 'selected' : ''; ?>">
			<a href="#" data-tab="sl-kiwi-tab-socialIdentity"><i class="dashicons dashicons-share"></i>
				<?php echo esc_html__( 'Social Identity', 'kiwi-social-share' ); ?>
			</a>
		</li>
        <li class="<?php echo ( $hash === 'sl-kiwi-tab-advanced' ) ? 'selected' : ''; ?>">
            <a href="#" data-tab="sl-kiwi-tab-advanced"><i class="dashicons dashicons-admin-settings"></i>
				<?php echo esc_html__( 'Advanced', 'kiwi-social-share' ); ?>
            </a>
        </li>
	</ul>
</div>