import React from 'react'
import { __ } from '@wordpress/i18n'
import {
  CheckboxControl,
  Button,
  Flex,
  FlexItem,
  FlexBlock
} from '@wordpress/components'
import { RefreshCw, Settings } from 'lucide-react'

interface RefreshControlsProps {
  selectedPosts: number[]
  onSelectionChange: (postIds: number[]) => void
  onBulkRefresh: () => void
  refreshing: boolean
}

export const RefreshControls: React.FC<RefreshControlsProps> = ({
  selectedPosts,
  onSelectionChange,
  onBulkRefresh,
  refreshing
}) => {
  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      // This would need to get all post IDs from the data
      // For now, just clear selection
      onSelectionChange([])
    } else {
      onSelectionChange([])
    }
  }

  return (
    <div className="hss-refresh-controls">
      <Flex>
        <FlexItem>
          <CheckboxControl
            label={__('Select All Posts', 'html-social-share-buttons')}
            checked={selectedPosts.length > 0}
            onChange={handleSelectAll}
          />
        </FlexItem>

        <FlexBlock />

        <FlexItem>
          <Button
            variant="secondary"
            onClick={onBulkRefresh}
            disabled={refreshing || selectedPosts.length === 0}
            icon={<RefreshCw size={16} />}
          >
            {refreshing
              ? __('Refreshing...', 'html-social-share-buttons')
              : __('Refresh Selected', 'html-social-share-buttons')
            }
          </Button>
        </FlexItem>

        <FlexItem>
          <Button
            variant="primary"
            icon={<Settings size={16} />}
          >
            {__('Settings', 'html-social-share-buttons')}
          </Button>
        </FlexItem>
      </Flex>

      {selectedPosts.length > 0 && (
        <p className="hss-selection-info">
          {__(`${selectedPosts.length} posts selected for refresh`, 'html-social-share-buttons')}
        </p>
      )}
    </div>
  )
}