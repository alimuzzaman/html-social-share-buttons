import React from 'react'
import { __ } from '@wordpress/i18n'
import {
  CheckboxControl,
  Button,
  __experimentalText as Text
} from '@wordpress/components'
import { Trash2, RefreshCw } from 'lucide-react'

interface ShareCountData {
  post_id: number
  title: string
  url: string
  share_counts: Record<string, number>
  total: number
  last_updated?: string
}

interface ShareCountsTableProps {
  data: ShareCountData[]
  selectedPosts: number[]
  onSelectionChange: (postIds: number[]) => void
}

export const ShareCountsTable: React.FC<ShareCountsTableProps> = ({
  data,
  selectedPosts,
  onSelectionChange
}) => {
  const handleSelectPost = (postId: number, checked: boolean) => {
    if (checked) {
      onSelectionChange([...selectedPosts, postId])
    } else {
      onSelectionChange(selectedPosts.filter(id => id !== postId))
    }
  }

  const formatNumber = (num: number): string => {
    if (num >= 1000000) {
      return (num / 1000000).toFixed(1) + 'M'
    }
    if (num >= 1000) {
      return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
  }

  const networks = ['facebook', 'twitter', 'pinterest', 'linkedin']

  if (!data || data.length === 0) {
    return (
      <div className="hss-empty-state">
        <Text variant="muted">
          {__('No share count data found. Select some posts and refresh to get started.', 'html-social-share-buttons')}
        </Text>
      </div>
    )
  }

  return (
    <div className="hss-share-counts-table">
      <table className="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th scope="col" className="check-column">
              <CheckboxControl
                checked={selectedPosts.length === data.length && data.length > 0}
                onChange={(checked) => {
                  if (checked) {
                    onSelectionChange(data.map(item => item.post_id))
                  } else {
                    onSelectionChange([])
                  }
                }}
              />
            </th>
            <th scope="col">{__('Post Title', 'html-social-share-buttons')}</th>
            {networks.map(network => (
              <th key={network} scope="col" className="hss-network-col">
                <span className={`hss-network-icon hss-network-${network}`}>
                  {network.charAt(0).toUpperCase() + network.slice(1)}
                </span>
              </th>
            ))}
            <th scope="col">{__('Total', 'html-social-share-buttons')}</th>
            <th scope="col">{__('Last Updated', 'html-social-share-buttons')}</th>
            <th scope="col">{__('Actions', 'html-social-share-buttons')}</th>
          </tr>
        </thead>
        <tbody>
          {data.map((item) => (
            <tr key={item.post_id}>
              <th scope="row" className="check-column">
                <CheckboxControl
                  checked={selectedPosts.includes(item.post_id)}
                  onChange={(checked) => handleSelectPost(item.post_id, checked)}
                />
              </th>
              <td>
                <strong>{item.title}</strong>
                <div className="row-actions">
                  <span>
                    <a href={item.url} target="_blank" rel="noopener noreferrer">
                      {__('View', 'html-social-share-buttons')}
                    </a>
                  </span>
                </div>
              </td>
              {networks.map(network => (
                <td key={network} className="hss-count-cell">
                  <span className="hss-count">
                    {formatNumber(item.share_counts[network] || 0)}
                  </span>
                </td>
              ))}
              <td className="hss-total-cell">
                <strong>{formatNumber(item.total)}</strong>
              </td>
              <td>
                {item.last_updated ? new Date(item.last_updated).toLocaleDateString() : __('Never', 'html-social-share-buttons')}
              </td>
              <td>
                <div className="hss-actions">
                  <Button
                    size="small"
                    variant="secondary"
                    icon={<RefreshCw size={14} />}
                    title={__('Refresh this post', 'html-social-share-buttons')}
                  />
                  <Button
                    size="small"
                    variant="secondary"
                    icon={<Trash2 size={14} />}
                    title={__('Delete counts for this post', 'html-social-share-buttons')}
                    isDestructive
                  />
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}