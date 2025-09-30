import { useState, useEffect, useCallback } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { PluginSettings, SaveSettingsResponse } from '../types';

/**
 * Hook for managing plugin settings state and API interactions
 */
export const useSettings = () => {
  const [settings, setSettings] = useState<PluginSettings | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isDirty, setIsDirty] = useState(false);

  // Load settings from API
  const loadSettings = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);

      const response = await apiFetch({
        path: '/html-social-share/v1/settings',
        method: 'GET',
      }) as any;

      // Flatten the response structure to match our PluginSettings interface
      const flatSettings: PluginSettings = {
        // General settings
        show_on_front_page: response.general?.show_on_front_page ?? true,
        show_on_posts: response.general?.show_on_posts ?? true,
        show_on_pages: response.general?.show_on_pages ?? false,
        show_on_archives: response.general?.show_on_archives ?? false,
        default_style: response.general?.default_style ?? 'default',
        default_size: response.general?.default_size ?? 'medium',

        // Network settings
        enabled_networks: response.networks?.enabled_networks ?? ['facebook', 'twitter', 'linkedin'],
        network_order: response.networks?.network_order ?? [],
        custom_networks: response.networks?.custom_networks ?? [],

        // Profile settings (placeholder)
        profiles: [],
        default_profile: '',

        // Integrations
        betterlinks_enabled: response.integrations?.betterlinks_enabled ?? false,
        betterlinks_api_key: response.integrations?.betterlinks_api_key ?? '',
        elementor_enabled: response.integrations?.elementor_enabled ?? false,
        divi_enabled: response.integrations?.divi_enabled ?? false,
        beaver_builder_enabled: response.integrations?.beaver_builder_enabled ?? false,

        // Appearance
        icon_style: response.appearance?.icon_style ?? 'default',
        button_size: response.appearance?.button_size ?? 'medium',
        button_spacing: response.appearance?.button_spacing ?? 5,
        custom_css: response.appearance?.custom_css ?? '',

        // Placement
        auto_placement: response.placement?.auto_placement ?? false,
        placement_position: response.placement?.placement_position ?? 'after',
        placement_post_types: response.placement?.placement_post_types ?? ['post'],

        // Advanced
        cache_enabled: response.advanced?.cache_enabled ?? true,
        cache_duration: response.advanced?.cache_duration ?? 3600,
        debug_mode: response.advanced?.debug_mode ?? false,
      };

      setSettings(flatSettings);
      setIsDirty(false);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load settings');
      console.error('Failed to load settings:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  // Update a specific setting
  const updateSetting = useCallback(<K extends keyof PluginSettings>(
    key: K,
    value: PluginSettings[K]
  ) => {
    setSettings(prev => {
      if (!prev) return prev;

      const updated = { ...prev, [key]: value };
      setIsDirty(true);
      return updated;
    });
  }, []);

  // Update multiple settings at once
  const updateSettings = useCallback((updates: Partial<PluginSettings>) => {
    setSettings(prev => {
      if (!prev) return prev;

      const updated = { ...prev, ...updates };
      setIsDirty(true);
      return updated;
    });
  }, []);

  // Save settings to API
  const saveSettings = useCallback(async (): Promise<SaveSettingsResponse> => {
    if (!settings) {
      throw new Error('No settings to save');
    }

    try {
      setSaving(true);
      setError(null);

      // Restructure settings to match API format
      const apiData = {
        general: {
          show_on_front_page: settings.show_on_front_page,
          show_on_posts: settings.show_on_posts,
          show_on_pages: settings.show_on_pages,
          show_on_archives: settings.show_on_archives,
          default_style: settings.default_style,
          default_size: settings.default_size,
        },
        networks: {
          enabled_networks: settings.enabled_networks,
          network_order: settings.network_order,
          custom_networks: settings.custom_networks,
        },
        appearance: {
          icon_style: settings.icon_style,
          button_size: settings.button_size,
          button_spacing: settings.button_spacing,
          custom_css: settings.custom_css,
        },
        placement: {
          auto_placement: settings.auto_placement,
          placement_position: settings.placement_position,
          placement_post_types: settings.placement_post_types,
        },
        integrations: {
          betterlinks_enabled: settings.betterlinks_enabled,
          betterlinks_api_key: settings.betterlinks_api_key,
          elementor_enabled: settings.elementor_enabled,
          divi_enabled: settings.divi_enabled,
          beaver_builder_enabled: settings.beaver_builder_enabled,
        },
        advanced: {
          cache_enabled: settings.cache_enabled,
          cache_duration: settings.cache_duration,
          debug_mode: settings.debug_mode,
        },
      };

      const response = await apiFetch({
        path: '/html-social-share/v1/settings',
        method: 'POST',
        data: apiData,
      }) as any;

      setIsDirty(false);

      return {
        success: response.success ?? true,
        message: response.message ?? 'Settings saved successfully',
        updated_settings: response.updated ?? {},
      };
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to save settings';
      setError(errorMessage);
      throw new Error(errorMessage);
    } finally {
      setSaving(false);
    }
  }, [settings]);

  // Reset settings to defaults
  const resetSettings = useCallback(async () => {
    try {
      setSaving(true);
      setError(null);

      await apiFetch({
        path: '/html-social-share/v1/settings/reset',
        method: 'POST',
      });

      // Reload settings after reset
      await loadSettings();
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to reset settings';
      setError(errorMessage);
      throw new Error(errorMessage);
    } finally {
      setSaving(false);
    }
  }, [loadSettings]);

  // Load settings on mount
  useEffect(() => {
    loadSettings();
  }, [loadSettings]);

  return {
    settings,
    loading,
    saving,
    error,
    isDirty,
    updateSetting,
    updateSettings,
    saveSettings,
    resetSettings,
    refreshSettings: loadSettings,
  };
};