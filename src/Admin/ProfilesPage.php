<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\ProfileManagerInterface;
use HtmlSocialShare\Networks;

class ProfilesPage
{
    private ProfileManagerInterface $profileManager;

    public function __construct(ProfileManagerInterface $profileManager)
    {
        $this->profileManager = $profileManager;
    }

    public function render()
    {
        // Handle form submissions
        $this->handleActions();

        $action = $_GET['action'] ?? 'list';
        $profileId = isset($_GET['id']) ? (int)$_GET['id'] : null;

        echo '<div class="wrap">';
        echo '<h1>Social Profiles</h1>';

        switch ($action) {
            case 'edit':
                $this->renderEditForm($profileId);
                break;
            case 'new':
                $this->renderEditForm();
                break;
            default:
                $this->renderProfilesList();
                break;
        }

        echo '</div>';
    }

    private function handleActions()
    {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'html_social_share_profiles')) {
            return;
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'update') {
            $this->handleSaveProfile($action);
        } elseif ($action === 'delete' && isset($_POST['profile_id'])) {
            $this->profileManager->deleteProfile((int)$_POST['profile_id']);
            echo '<div class="notice notice-success"><p>Profile deleted successfully.</p></div>';
        }
    }

    private function handleSaveProfile(string $action)
    {
        $data = [
            'network' => sanitize_text_field($_POST['network'] ?? ''),
            'handle' => sanitize_text_field($_POST['handle'] ?? ''),
            'url' => esc_url_raw($_POST['url'] ?? ''),
            'icon' => sanitize_text_field($_POST['icon'] ?? ''),
            'label' => sanitize_text_field($_POST['label'] ?? ''),
        ];

        // Validate required fields
        if (empty($data['network']) || empty($data['handle'])) {
            echo '<div class="notice notice-error"><p>Please fill in all required fields.</p></div>';
            return;
        }

        if ($action === 'create') {
            $this->profileManager->createProfile($data);
            echo '<div class="notice notice-success"><p>Profile created successfully.</p></div>';
        } elseif ($action === 'update' && isset($_POST['profile_id'])) {
            $this->profileManager->updateProfile((int)$_POST['profile_id'], $data);
            echo '<div class="notice notice-success"><p>Profile updated successfully.</p></div>';
        }
    }

    private function renderProfilesList()
    {
        $profiles = $this->profileManager->listProfiles();

        echo '<p>Manage your social media profiles for sharing buttons.</p>';

        echo '<div style="margin-bottom: 20px;">';
        echo '<a href="' . esc_url(add_query_arg('action', 'new')) . '" class="button button-primary">Add New Profile</a>';
        echo '</div>';

        if (empty($profiles)) {
            echo '<div class="notice notice-info"><p>No profiles found. <a href="' . esc_url(add_query_arg('action', 'new')) . '">Create your first profile</a>.</p></div>';
            return;
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">Network</th>';
        echo '<th scope="col">Handle</th>';
        echo '<th scope="col">Label</th>';
        echo '<th scope="col">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($profiles as $id => $profile) {
            echo '<tr>';
            echo '<td>' . esc_html(ucfirst($profile['network'] ?? 'Unknown')) . '</td>';
            echo '<td>' . esc_html($profile['handle'] ?? '') . '</td>';
            echo '<td>' . esc_html($profile['label'] ?? '') . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(add_query_arg(['action' => 'edit', 'id' => $id])) . '" class="button button-small">Edit</a> ';
            echo '<form method="post" style="display: inline;">';
            wp_nonce_field('html_social_share_profiles');
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="profile_id" value="' . esc_attr($id) . '">';
            echo '<button type="submit" class="button button-small button-link-delete" onclick="return confirm(\'Are you sure you want to delete this profile?\')">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    private function renderEditForm(?int $profileId = null)
    {
        $profile = null;
        $isEdit = $profileId !== null;

        if ($isEdit) {
            $profile = $this->profileManager->getProfile($profileId);
            if (!$profile) {
                echo '<div class="notice notice-error"><p>Profile not found.</p></div>';
                return;
            }
        }

        $title = $isEdit ? 'Edit Profile' : 'Add New Profile';
        echo '<h2>' . esc_html($title) . '</h2>';

        echo '<form method="post" action="' . esc_url(remove_query_arg(['action', 'id'])) . '">';
        wp_nonce_field('html_social_share_profiles');
        echo '<input type="hidden" name="action" value="' . ($isEdit ? 'update' : 'create') . '">';
        if ($isEdit) {
            echo '<input type="hidden" name="profile_id" value="' . esc_attr($profileId) . '">';
        }

        echo '<table class="form-table">';
        echo '<tbody>';

        // Network selection
        echo '<tr>';
        echo '<th scope="row"><label for="network">Social Network <span class="required">*</span></label></th>';
        echo '<td>';
        echo '<select id="network" name="network" required aria-describedby="network_desc">';
        echo '<option value="">Select Network</option>';
        $availableNetworks = Networks::getAvailableNetworks();
        foreach ($availableNetworks as $key => $network) {
            $selected = ($profile['network'] ?? '') === $key ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($network['label']) . '</option>';
        }
        echo '</select>';
        echo '<p id="network_desc" class="description">Choose the social network for this profile.</p>';
        echo '</td>';
        echo '</tr>';

        // Handle/Username
        echo '<tr>';
        echo '<th scope="row"><label for="handle">Handle/Username <span class="required">*</span></label></th>';
        echo '<td>';
        echo '<input type="text" id="handle" name="handle" value="' . esc_attr($profile['handle'] ?? '') . '" required aria-describedby="handle_desc">';
        echo '<p id="handle_desc" class="description">Your username or handle on this network (without @).</p>';
        echo '</td>';
        echo '</tr>';

        // Profile URL
        echo '<tr>';
        echo '<th scope="row"><label for="url">Profile URL</label></th>';
        echo '<td>';
        echo '<input type="url" id="url" name="url" value="' . esc_attr($profile['url'] ?? '') . '" aria-describedby="url_desc">';
        echo '<p id="url_desc" class="description">Optional: Link to your profile page.</p>';
        echo '</td>';
        echo '</tr>';

        // Display Label
        echo '<tr>';
        echo '<th scope="row"><label for="label">Display Label</label></th>';
        echo '<td>';
        echo '<input type="text" id="label" name="label" value="' . esc_attr($profile['label'] ?? '') . '" aria-describedby="label_desc">';
        echo '<p id="label_desc" class="description">Optional: Custom label to display instead of the handle.</p>';
        echo '</td>';
        echo '</tr>';

        // Icon picker
        echo '<tr>';
        echo '<th scope="row"><label for="icon">Icon</label></th>';
        echo '<td>';
        echo IconPicker::render('icon', $profile['icon'] ?? '', ['id' => 'profile_icon']);
        echo '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';

        echo '<div class="submit">';
        echo '<button type="submit" class="button button-primary">' . ($isEdit ? 'Update Profile' : 'Create Profile') . '</button> ';
        echo '<a href="' . esc_url(remove_query_arg(['action', 'id'])) . '" class="button">Cancel</a>';
        echo '</div>';

        echo '</form>';
    }
}