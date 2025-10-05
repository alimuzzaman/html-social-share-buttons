// Types for the HTML Social Share Buttons admin interface

export type BetterLinksCustomTracking = Record< string, string >;

export interface PluginSettings {
	// Display & Placement
	show_on_front_page: boolean;
	show_on_posts: boolean;
	show_on_pages: boolean;
	show_on_archives: boolean;
	auto_placement: boolean;
	placement_position: 'before' | 'after' | 'both' | 'left' | 'right';
	placement_post_types: string[];
	exclude_pages: string;
	// Legacy placement options (from zm_shbt_fld)
	floating_left: boolean;
	floating_right: boolean;
	before_content: boolean;
	after_content: boolean;

	// Design Defaults
	default_style: string;
	default_size: string;
	title: string;
	iconset: string;
	icon_style: 'default' | 'outline' | 'rounded' | 'square';
	button_size: 'small' | 'medium' | 'large';
	button_spacing: number;
	custom_css: string;

	// Network Settings
	enabled_networks: string[];
	network_order: string[];
	custom_networks: CustomNetwork[];

	// Profile Settings
	profiles: Profile[];
	default_profile: string;

	// Integrations
	betterlinks_enabled: boolean;
	betterlinks_api_key?: string;
	betterlinks_shorten_urls: boolean;
	betterlinks_add_tracking: boolean;
	betterlinks_custom_tracking: BetterLinksCustomTracking;
	betterlinks_available?: boolean;
	betterlinks_pro?: boolean;
	betterlinks_version?: string | null;
	elementor_enabled: boolean;
	divi_enabled: boolean;
	beaver_builder_enabled: boolean;

	// Advanced
	google_analytics: boolean;
	auto_hide_buttons: boolean;
	use_port_in_url: boolean;
	nofollow_links: boolean;
	cache_enabled: boolean;
	cache_duration: number;
	debug_mode: boolean;
}

export interface Profile {
	id: string;
	name: string;
	networks: { [ key: string ]: ProfileNetwork };
	display_settings: {
		style: string;
		size: string;
		text_labels: boolean;
		icon_only: boolean;
	};
}

export interface ProfileNetwork {
	enabled: boolean;
	label?: string;
	handle?: string;
	custom_url?: string;
}

export interface CustomNetwork {
	id: string;
	name: string;
	label: string;
	share_url: string;
	icon_class?: string;
	color?: string;
	enabled: boolean;
}

export interface NetworkConfig {
	id: string;
	name: string;
	label: string;
	share_url: string;
	requires_handle: boolean;
	icon_class: string;
	color: string;
	enabled?: boolean;
	description?: string;
}

export interface TabConfig {
	id: string;
	title: string;
	icon?: React.ReactNode;
	description?: string;
}

export interface NoticeProps {
	type: 'success' | 'warning' | 'error' | 'info';
	message: string;
	dismissible?: boolean;
	onDismiss?: () => void;
}

export interface FormFieldProps {
	label: string;
	description?: string;
	required?: boolean;
	error?: string;
	className?: string;
}

// API Response types
export interface ApiResponse< T = any > {
	success: boolean;
	data: T;
	message?: string;
}

export interface SaveSettingsResponse {
	success: boolean;
	message: string;
	updated_settings: Partial< PluginSettings >;
}

// WordPress REST API types
export interface WPRestSettings {
	[ key: string ]: any;
}

// Hook types
export interface UseSettingsReturn {
	settings: PluginSettings | null;
	loading: boolean;
	error: string | null;
	updateSettings: ( updates: Partial< PluginSettings > ) => Promise< void >;
	resetSettings: () => Promise< void >;
	saveSettings: () => Promise< void >;
	isDirty: boolean;
}
