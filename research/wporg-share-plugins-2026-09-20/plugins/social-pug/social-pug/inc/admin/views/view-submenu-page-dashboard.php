<?php
$dpsp_tab   = ( ! empty( $_GET['dpsp-tab'] ) ) ? strip_tags( $_GET['dpsp-tab'] ) : '';
$active_tab = ( ! empty( $dpsp_tab ) ? $dpsp_tab : 'dashboard' );

$dpsp_list_shares_selected = ( ! empty( $_GET['dpsp_list_shares'] ) ) ? strip_tags( $_GET['dpsp_list_shares'] ) : '';
$selected_list_shares = ( !empty( $dpsp_list_shares_selected ) ) ? $dpsp_list_shares_selected : 'most_shares';

$dpsp_list_attention_selected = ( ! empty( $_GET['dpsp_list_attention'] ) ) ? strip_tags( $_GET['dpsp_list_attention'] ) : '';
$selected_list_attention = ( ! empty( $dpsp_list_attention_selected ) ) ? $dpsp_list_attention_selected : 'social_information';

// Preload the lists
$preload_cached_posts_shares 	= dpsp_dashboard_get_post_list( $selected_list_shares );
$preload_cached_posts_attention = dpsp_dashboard_get_post_list( $selected_list_attention );

// Preload Hubbub settings
$settings = get_option( 'dpsp_settings', [] );

// Used to determine whether or not to show specific features
$is_plus_or_above = \Social_Pug::is_pro_plus_or_above();

// Link URLs for the Missing Information count
$missing_information_url = 		( $is_plus_or_above ) ? admin_url( 'admin.php?page=dpsp-dashboard&dpsp-tab=find-fix') : 'https://morehubbub.com/find-and-fix/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=missing-information';
$missing_information_target = 	( $is_plus_or_above ) ? '' : "target=\"_blank\"";
?>

<div class="dpsp-page-wrapper dpsp-page-dashboard wrap">
	<input type="hidden" name="_wp_http_referer" value="<?=admin_url( 'admin.php?page=dpsp-dashboard' );?>">
	<h1><?php esc_html_e( 'Dashboard', 'social-pug' ); ?></h1>

	<!-- Navigation Tabs -->
	<div class="dpsp-card navigation">
		<ul class="dpsp-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $tab_slug => $tab_name ) :
				
			?>
				<li class="dpsp-nav-tab <?php echo ( $tab_slug === $active_tab ? 'dpsp-active' : '' ); ?>" data-tab="<?php echo esc_attr( $tab_slug ); ?>">
					<a href="<?=admin_url( 'admin.php?page=dpsp-dashboard&dpsp-tab=' . $tab_slug );?>">
						<?php echo esc_attr( $tab_name ); ?>
				
						<?php if ( $tab_slug == 'find-fix' ) { ?>
							<span class="dpsp-tab-badge" title="The number of posts that may be missing social media data"><?php echo ( ! get_transient( 'dpsp_dashboard_count_requires_attention' ) ) ? 0 : number_format( get_transient( 'dpsp_dashboard_count_requires_attention' ), 0, '', ',' ); ?></span>
						<?php } ?>
					</a>
					
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<!-- Dashboard Tab Content -->
	<div id="dpsp-tab-dashboard" class="dpsp-tab <?php echo ( 'dashboard' === $active_tab ? 'dpsp-active' : '' ); ?>">

		<div class="dpsp-card-group">
			<!-- Latest News -->
			<div class="dpsp-card dpsp-card-news">

				<div class="dpsp-card-header">
					<?php esc_html_e( 'Latest News', 'social-pug' ); ?>
					<span class="social-network-icons"> 
						<span class="social-network-icon facebook">
							<a href="https://www.facebook.com/nerdpress.net" target="_blank" title="Follow NerdPress on Facebook">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 18 32"><path d="M17.12 0.224v4.704h-2.784q-1.536 0-2.080 0.64t-0.544 1.92v3.392h5.248l-0.704 5.28h-4.544v13.568h-5.472v-13.568h-4.544v-5.28h4.544v-3.904q0-3.328 1.856-5.152t4.96-1.824q2.624 0 4.064 0.224z"></path></svg>
							</a>
						</span>
						<span class="social-network-icon instagram">
							<a href="https://instagram.com/nerdpressteam" target="_blank" title="Follow NerdPress on Instagram">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 27 32"><path d="M18.272 16q0-1.888-1.312-3.232t-3.232-1.344-3.232 1.344-1.344 3.232 1.344 3.232 3.232 1.344 3.232-1.344 1.312-3.232zM20.736 16q0 2.912-2.048 4.992t-4.96 2.048-4.992-2.048-2.048-4.992 2.048-4.992 4.992-2.048 4.96 2.048 2.048 4.992zM22.688 8.672q0 0.672-0.48 1.152t-1.184 0.48-1.152-0.48-0.48-1.152 0.48-1.152 1.152-0.48 1.184 0.48 0.48 1.152zM13.728 4.736q-0.128 0-1.376 0t-1.888 0-1.728 0.064-1.824 0.16-1.28 0.352q-0.896 0.352-1.568 1.024t-1.056 1.568q-0.192 0.512-0.32 1.28t-0.192 1.856-0.032 1.696 0 1.888 0 1.376 0 1.376 0 1.888 0.032 1.696 0.192 1.856 0.32 1.28q0.384 0.896 1.056 1.568t1.568 1.024q0.512 0.192 1.28 0.352t1.824 0.16 1.728 0.064 1.888 0 1.376 0 1.344 0 1.888 0 1.728-0.064 1.856-0.16 1.248-0.352q0.896-0.352 1.6-1.024t1.024-1.568q0.192-0.512 0.32-1.28t0.192-1.856 0.032-1.696 0-1.888 0-1.376 0-1.376 0-1.888-0.032-1.696-0.192-1.856-0.32-1.28q-0.352-0.896-1.024-1.568t-1.6-1.024q-0.512-0.192-1.248-0.352t-1.856-0.16-1.728-0.064-1.888 0-1.344 0zM27.424 16q0 4.096-0.096 5.664-0.16 3.712-2.208 5.76t-5.728 2.208q-1.6 0.096-5.664 0.096t-5.664-0.096q-3.712-0.192-5.76-2.208t-2.208-5.76q-0.096-1.568-0.096-5.664t0.096-5.664q0.16-3.712 2.208-5.76t5.76-2.208q1.568-0.096 5.664-0.096t5.664 0.096q3.712 0.192 5.728 2.208t2.208 5.76q0.096 1.568 0.096 5.664z"></path></svg>
							</a>
						</span>
					</span>
				</div>

				<div class="dpsp-card-inner">

					<?php echo dpsp_dashboard_display_news( 10 ); ?>

				</div>
			</div> <!-- End Latest News -->
			
			<!-- Wide Ad -->
			<div class="dpsp-card dpsp-card-ad dpsp-card-ad-wide">

				<div class="dpsp-card-inner">
					<?php if ( Social_Pug::is_free() ) { ?>
						<script src="https://api.morehubbub.com/nan/?p=hubbub-lite&v=<?php echo HUBBUB_VERSION; ?>&d=<?php echo get_site_url(); ?>&l=<?=get_option ( 'mv_grow_license_status' );?>&s=300x250"></script>
					<?php } else { ?>
						<script src="https://api.morehubbub.com/nan/?p=hubbub-pro&v=<?php echo HUBBUB_VERSION; ?>&d=<?php echo get_site_url(); ?>&l=<?=get_option ( 'mv_grow_license_status' );?>&s=300x250"></script>
					<?php } ?>
				</div>

			</div> <!-- End Wide Ad -->
		</div>
		
		<div class="dpsp-card-group">
		<!-- Counts -->
		<div class="dpsp-card dpsp-card-counts">

			<div class="dpsp-card-inner">

				<div class="dpsp-counts-item">
					<h3>Total Shares</h3>
					<p><?php echo number_format( dpsp_show_total_count( 'dpsp_networks_shares_total' ), 0, '', ',' ); ?></p>
				</div>

				<div class="dpsp-counts-item">
					<h3>Total Saves</h3>
					<p><?php
						if ( $is_plus_or_above ) {
							echo number_format( dpsp_show_total_count( 'dpsp_save_this_count' ), 0, '', ',' );
						} else {
							echo '<a href="https://morehubbub.com/save-this/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=share-padlocks" target="_blank" style="text-decoration: none;" title="Unlock Hubbub Save This by upgrading to Hubbub Pro+. Click to learn more.">🔒</a>';
						} ?>
					</p>
				</div>

				<div class="dpsp-counts-item warning">
					<h3>Missing Information</h3>
					<p><a href="<?=$missing_information_url;?>" <?=$missing_information_target;?>><?php
					echo ( ! get_transient( 'dpsp_dashboard_count_requires_attention' ) ) ? 0 : number_format( get_transient( 'dpsp_dashboard_count_requires_attention' ), 0, '', ',' );
					?></a></p>
				</div>

			</div>
		</div> <!-- End Counts -->

		<!-- Ad -->
		<div class="dpsp-card dpsp-card-ad">

			<div class="dpsp-card-inner">
				<?php if ( Social_Pug::is_free() ) { ?>
					<script src="https://api.morehubbub.com/nan/?p=hubbub-lite&v=<?php echo HUBBUB_VERSION; ?>&d=<?php echo get_site_url(); ?>&l=<?=get_option ( 'mv_grow_license_status' );?>&s=300x250"></script>
				<?php } else { ?>
					<script src="https://api.morehubbub.com/nan/?p=hubbub-pro&v=<?php echo HUBBUB_VERSION; ?>&d=<?php echo get_site_url(); ?>&l=<?=get_option ( 'mv_grow_license_status' );?>&s=300x250"></script>
				<?php } ?>
			</div>

		</div> <!-- End Ad -->

		</div> <!-- End card group -->

		<!-- Engagement Stats -->
		<div class="dpsp-card dpsp-card-list-shares">

			<div class="dpsp-card-header">
				<?php esc_html_e( 'Engagement Stats', 'social-pug' ); ?>

				<div class="dpsp-list-dropdown">
					<select id="dpsp_list_shares" name="dpsp_list_shares">
						<option value="most_shares" <?php echo ( $selected_list_shares=='most_shares') ? 'selected' : '';?>>Most Shares</option>
						<option value="most_saves" <?php echo ( $selected_list_shares=='most_saves') ? 'selected' : '';?>>Most Saves</option>
						<option value="recently_published" <?php echo ( $selected_list_shares=='recently_published') ? 'selected' : '';?>>Recently Published</option>
						<option value="fewest_shares" <?php echo ( $selected_list_shares=='fewest_shares') ? 'selected' : '';?>>Fewest Shares</option>
						<option value="fewest_saves" <?php echo ( $selected_list_shares=='fewest_saves') ? 'selected' : '';?>>Fewest Saves</option>
					</select>
				</div>
			</div>

			<div class="dpsp-card-inner">

			<?php $cached_posts = $preload_cached_posts_shares;

				if ( count( $cached_posts ) == 0 ) { ?>
				
					<p class="no-results"><?php esc_html_e('It looks like you might just be getting started. No posts to show here yet.', 'social-pug'); ?></p>

				<?php } else { ?>

					<table class="dpsp-table-shares" width="100%">
						<tr>
							<th></th>
							<th class="post-date"></th>
							<th class="shares">Shares</th>
							<th class="saves">Saves</th>
						</tr>
						<?php
						foreach( $cached_posts as $post ) : ?>
								<tr data-post-id="<?=$post['id'];?>">
									<td class="post-title"><a href="<?=$post['permalink'];?>" target="_blank"><?=$post['title'];?></a> <span class="edit"><a href="<?=admin_url( 'post.php?post=' . $post['id'] . '&action=edit' );?>" target="_blank"><span class="dashicons dashicons-edit"></span></a></span></td>
									<td class="post-date"><?=$post['post_date'];?></td>
									<td class="shares"><?=number_format( $post['shares_count'], 0, '', ',' );?></td>
									<td class="saves">
										<?php
										if ( $is_plus_or_above ) :
											echo number_format( $post['saves_count'], 0, '', ',' );
										else :
											echo '<a href="https://morehubbub.com/save-this/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=share-padlocks" target="_blank" style="text-decoration: none;" title="Unlock Hubbub Save This by upgrading to Hubbub Pro+. Click to learn more.">🔒</a>';
										endif;
										?>
									</td>
								</tr>
						
						<?php

						endforeach; ?>
					</table>

					<?php if ( \Social_Pug::is_free() ) { ?>
					<p class="dpsp-engagement-stats-lite-notice">💡 Hubbub Lite shows only your top 10 posts—<a href="https://morehubbub.com/pricing/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=lite-footer-upgrade" target="_blank">upgrade</a> to Pro for the top 50 (and a <em>ton</em> of additional features and settings), or go Pro+ to unlock <a href="https://morehubbub.com/save-this/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=lite-footer-save-this" target="_blank">Save This</a> and <a href="https://morehubbub.com/find-and-fix/?utm_source=hubbub_plugin&utm_medium=dashboard&utm_campaign=lite-footer-find-and-fix" target="_blank">Find & Fix</a>!</p>
				<?php } ?>
				<?php } ?>
			</div>
		</div> <!-- End Shares and Saves -->

	</div> <!-- End Dashboard tab -->

	<?php if ( ! \Social_Pug::is_free() ) {
		include( \Mediavine\Grow\View_Loader::$plugin_path . '/inc/admin/views/view-submenu-page-dashboard-tab-missing-information.php' );
	} ?>
	
	<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-made-with-love.php' ); ?>
	<?php echo \Mediavine\Grow\View_Loader::get_view( '/inc/admin/views/view-footer-unlock-features.php' ); ?>
</div>