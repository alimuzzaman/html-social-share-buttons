import React, { useState } from 'react';
import { FormField, TextInput, Checkbox, Button, LoadingOverlay } from '../ui';
import { NetworkConfig } from '../../types';
import { useNetworksContext } from '../../contexts';
import { useNotifications } from '../../contexts';
import { Facebook, Twitter, Linkedin, Pinterest, Reddit, MessageSquare } from 'lucide-react';

const defaultNetworks: NetworkConfig[] = [
  {
    id: 'facebook',
    name: 'Facebook',
    label: 'Facebook',
    share_url: 'https://www.facebook.com/sharer/sharer.php?u={url}',
    requires_handle: false,
    icon_class: 'fab fa-facebook-f',
    color: '#1877f2'
  },
  {
    id: 'twitter',
    name: 'Twitter',
    label: 'Twitter',
    share_url: 'https://twitter.com/intent/tweet?url={url}&text={title}',
    requires_handle: false,
    icon_class: 'fab fa-twitter',
    color: '#1da1f2'
  },
  {
    id: 'linkedin',
    name: 'LinkedIn',
    label: 'LinkedIn',
    share_url: 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
    requires_handle: false,
    icon_class: 'fab fa-linkedin-in',
    color: '#0077b5'
  },
  {
    id: 'pinterest',
    name: 'Pinterest',
    label: 'Pinterest',
    share_url: 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
    requires_handle: false,
    icon_class: 'fab fa-pinterest-p',
    color: '#bd081c'
  },
  {
    id: 'reddit',
    name: 'Reddit',
    label: 'Reddit',
    share_url: 'https://reddit.com/submit?url={url}&title={title}',
    requires_handle: false,
    icon_class: 'fab fa-reddit-alien',
    color: '#ff4500'
  },
  {
    id: 'whatsapp',
    name: 'WhatsApp',
    label: 'WhatsApp',
    share_url: 'https://wa.me/?text={title}%20{url}',
    requires_handle: false,
    icon_class: 'fab fa-whatsapp',
    color: '#25d366'
  }
];

// Prefer lucide-react icon components where available, fall back to plugin icon assets, then initial-letter
const networkLucideMap: Record<string, React.ReactNode | undefined> = {
  facebook: <Facebook size={16} />,
  twitter: <Twitter size={16} />,
  linkedin: <Linkedin size={16} />,
  pinterest: <Pinterest size={16} />,
  reddit: <Reddit size={16} />,
  whatsapp: <MessageSquare size={16} />,
};

const pluginUrl = (typeof window !== 'undefined' && (window as any).hssAdminConfig && (window as any).hssAdminConfig.pluginUrl) ? (window as any).hssAdminConfig.pluginUrl : '';

export const NetworksTab: React.FC = () => {
  const { networks: apiNetworks, updateNetwork, loading } = useNetworksContext();
  const { showSuccess, showError } = useNotifications();

  // Keep local state for immediate UI updates and form handling
  const [localNetworks] = useState<NetworkConfig[]>(defaultNetworks);
  const [enabledNetworks, setEnabledNetworks] = useState<string[]>(['facebook', 'twitter', 'linkedin']);
  const [isSaving, setIsSaving] = useState(false);  // Use API networks if available, otherwise fall back to local defaults
  const networks = apiNetworks.length > 0 ? apiNetworks : localNetworks;

  const handleNetworkToggle = (networkId: string, enabled: boolean) => {
    if (enabled) {
      setEnabledNetworks(prev => [...prev, networkId]);
    } else {
      setEnabledNetworks(prev => prev.filter(id => id !== networkId));
    }
  };

  const handleNetworkLabelChange = async (networkId: string, label: string) => {
    try {
      // Update via API if available
      if (apiNetworks.length > 0) {
        await updateNetwork(networkId, { label });
      }

      showSuccess(`${label} label updated!`);
    } catch (error) {
      showError('Failed to update network label', 'Please try again.');
    }
  };  const handleSave = async () => {
    setIsSaving(true);
    try {
      // Save enabled networks configuration
      const networkUpdates = networks.map((network: NetworkConfig) => ({
        ...network,
        enabled: enabledNetworks.includes(network.id)
      }));

      // If API is available, save via API
      if (apiNetworks.length > 0) {
        await Promise.all(
          networkUpdates.map((network: NetworkConfig) =>
            updateNetwork(network.id, { enabled: network.enabled })
          )
        );
      }

      showSuccess('Network settings saved successfully!');
    } catch (error) {
      showError('Failed to save settings', 'Please try again.');
    } finally {
      setIsSaving(false);
    }
  };

  return (
    <div className="networks-tab">
      <div className="bg-white border border-gray-200 rounded shadow-sm p-6">
        <h2 className="text-xl font-semibold mb-4">Social Networks</h2>
        <p className="text-gray-600 mb-6">
          Choose which social networks to make available for sharing and customize their appearance.
        </p>



        <div className="space-y-4">
          <h3 className="text-lg font-medium text-gray-800 mb-3">Available Networks</h3>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {networks.map((network: NetworkConfig) => {
              const isEnabled = enabledNetworks.includes(network.id);

              return (
                <div
                  key={network.id}
                  className={`transition-all duration-200 border rounded-lg p-4 cursor-pointer hover:shadow-md ${
                    isEnabled ? 'border-blue-500 bg-blue-50 hover:bg-blue-100' : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex items-center mb-3">
                    <div
                      className="w-8 h-8 rounded flex items-center justify-center mr-3"
                      style={{ backgroundColor: network.color }}
                    >
                      {networkLucideMap[network.id] ? (
                        <span className="text-white" aria-hidden>{networkLucideMap[network.id]}</span>
                      ) : (
                        (() => {
                          const imgSrc = `${pluginUrl}assets/iconset/default_square/${network.id}.png`;
                          return (
                            <img
                              src={imgSrc}
                              alt={`${network.name} icon`}
                              className="w-5 h-5"
                              onError={(e) => {
                                (e.currentTarget as HTMLImageElement).style.display = 'none';
                                const placeholder = document.createElement('span');
                                placeholder.className = 'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-xs text-gray-700';
                                placeholder.textContent = network.name.charAt(0);
                                e.currentTarget.parentElement?.appendChild(placeholder);
                              }}
                            />
                          );
                        })()
                      )}
                    </div>
                    <div className="flex-1">
                      <h4 className="font-medium text-gray-800">{network.name}</h4>
                    </div>
                    <Checkbox
                      checked={isEnabled}
                      onChange={(checked) => handleNetworkToggle(network.id, checked)}
                      label=""
                    />
                  </div>

                  {isEnabled && (
                    <div className="mt-3">
                      <FormField
                        label="Button Label"
                        description="Text displayed on the button"
                      >
                        <TextInput
                          value={network.label}
                          onChange={(value) => handleNetworkLabelChange(network.id, value)}
                          placeholder={network.name}
                        />
                      </FormField>
                    </div>
                  )}
                </div>
              );
            })}
          </div>

          <div className="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 className="font-medium text-gray-800 mb-2">Network Order</h4>
            <p className="text-sm text-gray-600 mb-3">
              Drag and drop to reorder the networks as they will appear on your site.
            </p>
            <div className="flex flex-wrap gap-2">
              {enabledNetworks.map((networkId) => {
                const network = networks.find((n: NetworkConfig) => n.id === networkId);
                if (!network) return null;

                return (
                  <div
                    key={networkId}
                    className="flex items-center px-3 py-1 bg-white border border-gray-200 rounded cursor-move transition-all duration-200 hover:shadow-sm hover:border-gray-300"
                  >
                    <div
                      className="w-4 h-4 rounded mr-2"
                      style={{ backgroundColor: network.color }}
                    />
                    <span className="text-sm">{network.label}</span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>

        <div className="mt-8 pt-4 border-t border-gray-200">
          <div className="flex justify-between items-center">
            <p className="text-sm text-gray-600">
              {enabledNetworks.length} network{enabledNetworks.length !== 1 ? 's' : ''} enabled
            </p>
            <Button
              onClick={handleSave}
              loading={isSaving}
              variant="primary"
            >
              Save Changes
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
};