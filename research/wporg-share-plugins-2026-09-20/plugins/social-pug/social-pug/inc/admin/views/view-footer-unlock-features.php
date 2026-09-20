<?php 
    $hubbub_currently_logged_in_user    = wp_get_current_user();
    $hubbub_admin_email                 = $hubbub_currently_logged_in_user->user_email;
    $hubbub_nonce_token                 = wp_create_nonce( 'dpsp_nonce_lite_save_and_activate_license' ); // Used to register Lite license
?>
<div id="dpsp-card-unlock-features" class="dpsp-card dpsp-card-unlock-features" data-admin-email="<?=$hubbub_admin_email;?>" data-nonce-token="<?=$hubbub_nonce_token;?>">
    <div class="dpsp-card-inner" id="dpsp-modal-unlock-features"></div>
</div>
