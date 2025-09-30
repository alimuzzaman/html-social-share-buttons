import React from 'react'
import { __ } from '@wordpress/i18n'
import { AdminIcon } from './ui/Icons'
import { Button, Checkbox } from './ui'
import { RefreshCw, Trash2 } from 'lucide-react'

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
        <p className="text-sm text-gray-500">
          {__('No share count data found. Select some posts and refresh to get started.', 'html-social-share-buttons')}
        </p>
      </div>
    )
  }

  return (
    <div className="hss-share-counts-table">
      <table className="w-full table-fixed border-collapse border border-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th scope="col" className="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
              <Checkbox
                checked={selectedPosts.length === data.length && data.length > 0}
                onChange={(checked) => {
                  if (checked) {
                    onSelectionChange(data.map(item => item.post_id))
                  } else {
                    onSelectionChange([])
                  }
                }}
                label=""
              />
            </th>
            <th scope="col" className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">{__('Post Title', 'html-social-share-buttons')}</th>
            {networks.map(network => (
              <th key={network} scope="col" className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                <span className={`hss-network-icon hss-network-${network}`}>
                  {network.charAt(0).toUpperCase() + network.slice(1)}
                </span>
              </th>
            ))}
            <th scope="col" className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">{__('Total', 'html-social-share-buttons')}</th>
            <th scope="col" className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">{__('Last Updated', 'html-social-share-buttons')}</th>
            <th scope="col" className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">{__('Actions', 'html-social-share-buttons')}</th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {data.map((item, index) => (
            <tr key={item.post_id} className={`${index % 2 === 0 ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 hover:bg-gray-100'} transition-colors duration-150`}>
              <th scope="row" className="px-3 py-2 border-b border-gray-200">
                <Checkbox
                  checked={selectedPosts.includes(item.post_id)}
                  onChange={(checked) => handleSelectPost(item.post_id, checked)}
                  label=""
                />
              </th>
              <td className="px-3 py-2 border-b border-gray-200">
                <strong>{item.title}</strong>
                <div className="row-actions">
                  <span>
                    <a className="text-sm text-blue-600 hover:underline" href={item.url} target="_blank" rel="noopener noreferrer">
                      {__('View', 'html-social-share-buttons')}
                    </a>
                  </span>
                </div>
              </td>
              {networks.map(network => (
                <td key={network} className="px-3 py-2 border-b border-gray-200">
                  <span className="text-sm text-gray-700">
                    {formatNumber(item.share_counts[network] || 0)}
                  </span>
                </td>
              ))}
              <td className="px-3 py-2 border-b border-gray-200">
                <strong>{formatNumber(item.total)}</strong>
              </td>
              <td className="px-3 py-2 border-b border-gray-200">
                {item.last_updated ? new Date(item.last_updated).toLocaleDateString() : __('Never', 'html-social-share-buttons')}
              </td>
              <td className="px-3 py-2 border-b border-gray-200">
                <div className="hss-actions flex items-center space-x-2">
                  <Button
                    size="small"
                    variant="secondary"
                    className="transition-all duration-200 hover:shadow-sm flex items-center space-x-2"
                    onClick={() => { /* handled by parent */ }}
                  >
                    <AdminIcon candidates={["update","refresh","arrowClockwise","redo"]} lucide={<RefreshCw size={14} />} size={14} className="text-current" />
                    <span className="text-sm">{__('Refresh', 'html-social-share-buttons')}</span>
                  </Button>
                  <Button
                    size="small"
                    variant="secondary"
                    className="transition-all duration-200 hover:shadow-sm text-red-600 hover:text-red-700 hover:bg-red-50 flex items-center space-x-2"
                    onClick={() => { /* handled by parent */ }}
                  >
                    <AdminIcon candidates={["trash","remove","delete"]} lucide={<Trash2 size={14} />} size={14} className="text-current" />
                    <span className="text-sm">{__('Delete', 'html-social-share-buttons')}</span>
                  </Button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}