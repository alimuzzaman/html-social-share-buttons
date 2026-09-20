<?php
/**
 * Admin panel template.
 *
 * The template wrapper for the admin panel.
 *
 * @package SimpleShareButtonsAdder
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$selected_tab = get_option( 'ssba_selected_tab' );
$selected_tab = null !== $selected_tab && false !== $selected_tab ? $selected_tab : 'modern';
$modern       = isset( $selected_tab ) && false === in_array( $selected_tab, array( 'bar', 'gdpr' ), true ) ? 'active' : '';
$bar          = isset( $selected_tab ) && 'bar' === $selected_tab ? 'active' : '';
$gdpr         = isset( $selected_tab ) && 'gdpr' === $selected_tab ? 'active' : '';

$accept_terms = filter_input( INPUT_GET, 'accept-terms', FILTER_UNSAFE_RAW );
$nonce        = filter_input( INPUT_GET, 'nonce', FILTER_UNSAFE_RAW );
$accept_terms = sanitize_text_field( wp_unslash( $accept_terms ) );

echo $this->admin_header(); // phpcs:ignore
echo $this->forms->open( false ); // phpcs:ignore
?>
<h2><?php echo esc_html__( 'Settings', 'simple-share-buttons-adder' ); ?></h2>

<ul class="nav nav-tabs">
	<li class="ssba-modern-tab <?php echo esc_attr( $modern ); ?>">
		<a href="#plus-share-buttons" data-toggle="tab">
			<?php echo esc_html__( 'Modern Share Buttons', 'simple-share-buttons-adder' ); ?>
		</a>
	</li>
	<li class="ssba-bar-tab <?php echo esc_attr( $bar ); ?>">
		<a href="#share-bar" data-toggle="tab">
			<?php echo esc_html__( 'Share Bar', 'simple-share-buttons-adder' ); ?>
		</a>
	</li>
	<li class="ssba-gdpr <?php echo esc_attr( $gdpr ); ?>">
		<span class="ssba-new-icon">new</span>
		<a href="#gdpr" data-toggle="tab">
			<?php echo esc_html__( 'GDPR', 'simple-share-buttons-adder' ); ?>
		</a>
	</li>
</ul>
<div id="ssbaTabContent" class="tab-content">
	<?php require_once DIR_PATH . "templates/classic-tab.php"; ?>
	<?php require_once DIR_PATH . "templates/plus-tab.php"; ?>
	<?php require_once DIR_PATH . "templates/share-bar-tab.php"; ?>
	<?php require_once DIR_PATH . "templates/gdpr-tab.php"; ?>
</div>
<input id="ssba_selected_tab" name="ssba_selected_tab" type="hidden" value="<?php echo esc_html( $selected_tab ); ?>"/>
<?php
echo $this->forms->close(); // phpcs:ignore
echo $this->admin_footer(); // phpcs:ignore
?>
