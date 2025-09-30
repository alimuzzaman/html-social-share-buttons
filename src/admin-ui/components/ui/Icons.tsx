import React, { useEffect, useState } from 'react';
import { RefreshCw, Trash2, Settings as LucideSettings } from 'lucide-react';

interface IconProps {
  size?: number;
  className?: string;
}

export const RefreshIcon: React.FC<IconProps> = ({ size = 16, className = '' }) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width={size}
    height={size}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className={className}
    aria-hidden="true"
  >
    <polyline points="23 4 23 10 17 10" />
    <polyline points="1 20 1 14 7 14" />
    <path d="M3.51 9a9 9 0 0114.13-3.36L23 10" />
    <path d="M20.49 15a9 9 0 01-14.13 3.36L1 14" />
  </svg>
);

export const TrashIcon: React.FC<IconProps> = ({ size = 16, className = '' }) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width={size}
    height={size}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className={className}
    aria-hidden="true"
  >
    <polyline points="3 6 5 6 21 6" />
    <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
    <path d="M10 11v6" />
    <path d="M14 11v6" />
    <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
  </svg>
);

export const SettingsIcon: React.FC<IconProps> = ({ size = 16, className = '' }) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width={size}
    height={size}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className={className}
    aria-hidden="true"
  >
    <circle cx="12" cy="12" r="3" />
    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06A2 2 0 017.31 2.7l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09c0 .56.36 1.06 1 1.51h.41a1.65 1.65 0 001.82-.33l.06-.06A2 2 0 1120.7 7.31l-.06.06a1.65 1.65 0 00-.33 1.82V9c.45.64 1 1 1.51 1H21a2 2 0 010 4h-.09c-.51 0-1.06.36-1.51 1z" />
  </svg>
);

type AdminIconProps = {
  candidates?: string[]; // candidate export names to look for in @wordpress/icons
  lucide?: React.ReactNode; // lucide-react fallback element
  size?: number;
  className?: string;
};

export const AdminIcon: React.FC<AdminIconProps> = ({ candidates = [], lucide = null, size = 16, className = '' }) => {
  const [WpIcon, setWpIcon] = useState<React.ComponentType<any> | null>(null);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const icons = await import('@wordpress/icons');
        for (const candidate of candidates) {
          if (icons && (icons as any)[candidate]) {
            if (!mounted) return;
            setWpIcon(() => (icons as any)[candidate]);
            return;
          }
        }
      } catch (err) {
        // @wordpress/icons not available or failed to load, ignore
      }
    })();

    return () => { mounted = false };
  }, [candidates]);

  if (WpIcon) {
    return <WpIcon size={size} className={className} /> as any;
  }

  if (lucide) {
    return <span className={className}>{lucide}</span> as any;
  }

  // Fallback to local svg icons based on best guess from candidates
  if (candidates.includes('trash')) return <TrashIcon size={size} className={className} />;
  if (candidates.includes('refresh') || candidates.includes('update')) return <RefreshIcon size={size} className={className} />;
  if (candidates.includes('settings')) return <SettingsIcon size={size} className={className} />;

  // Default fallback
  return <span className={className}><RefreshIcon size={size} /></span> as any;
};
