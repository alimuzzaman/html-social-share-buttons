import React, { useState, useEffect } from 'react'
import { __ } from '@wordpress/i18n'
import {
  Panel,
  PanelBody,
  Card,
  CardBody,
  Spinner,
  Notice,
  FlexBlock,
  Flex
} from '@wordpress/components'
import { ShareCountsTable } from './ShareCountsTable'
import { RefreshControls } from './RefreshControls'
import { useShareCounts } from '../hooks/useShareCounts'
import { useRestApi } from '../hooks/useRestApi'

export const AdminInterface: React.FC = () => {
  const [selectedPosts, setSelectedPosts] = useState<number[]>([])
  const [refreshing, setRefreshing] = useState(false)
  const [notification, setNotification] = useState<{
    type: 'success' | 'error' | 'info'
    message: string
  } | null>(null)

  const {
    shareCounts,
    loading,
    error,
    refreshShareCounts
  } = useShareCounts(selectedPosts)

  const { bulkRefresh } = useRestApi()

  useEffect(() => {
    if (error) {
      setNotification({
        type: 'error',
        message: error
      })
    }
  }, [error])

  const handleBulkRefresh = async () => {
    if (selectedPosts.length === 0) {
      setNotification({
        type: 'error',
        message: __('Please select posts to refresh', 'html-social-share-buttons')
      })
      return
    }

    setRefreshing(true)
    try {
      const result = await bulkRefresh(selectedPosts)

      setNotification({
        type: 'success',
        message: __(`Successfully refreshed ${result.processed} posts`, 'html-social-share-buttons')
      })

      // Refresh the displayed data
      await refreshShareCounts()
    } catch (err) {
      setNotification({
        type: 'error',
        message: __('Failed to refresh share counts', 'html-social-share-buttons')
      })
    } finally {
      setRefreshing(false)
    }
  }

  const clearNotification = () => {
    setNotification(null)
  }

  return (
    <div className="hss-admin-interface">
      <div className="wrap">
        <h1>{__('Social Share Counts Manager', 'html-social-share-buttons')}</h1>

        {notification && (
          <Notice
            status={notification.type}
            onRemove={clearNotification}
            isDismissible
          >
            {notification.message}
          </Notice>
        )}

        <Panel>
          <PanelBody
            title={__('Refresh Controls', 'html-social-share-buttons')}
            initialOpen={true}
          >
            <Card>
              <CardBody>
                <RefreshControls
                  selectedPosts={selectedPosts}
                  onSelectionChange={setSelectedPosts}
                  onBulkRefresh={handleBulkRefresh}
                  refreshing={refreshing}
                />
              </CardBody>
            </Card>
          </PanelBody>

          <PanelBody
            title={__('Share Counts Data', 'html-social-share-buttons')}
            initialOpen={true}
          >
            <Card>
              <CardBody>
                {loading && (
                  <Flex justify="center">
                    <FlexBlock>
                      <Spinner
                        onPointerEnterCapture={undefined}
                        onPointerLeaveCapture={undefined}
                      />
                      <span style={{ marginLeft: '10px' }}>
                        {__('Loading share counts...', 'html-social-share-buttons')}
                      </span>
                    </FlexBlock>
                  </Flex>
                )}

                {!loading && shareCounts && (
                  <ShareCountsTable
                    data={shareCounts}
                    selectedPosts={selectedPosts}
                    onSelectionChange={setSelectedPosts}
                  />
                )}
              </CardBody>
            </Card>
          </PanelBody>

          <PanelBody
            title={__('Statistics', 'html-social-share-buttons')}
            initialOpen={false}
          >
            <Card>
              <CardBody>
                <div className="hss-stats">
                  <p>
                    <strong>{__('Total Posts with Counts:', 'html-social-share-buttons')}</strong>{' '}
                    {shareCounts?.length || 0}
                  </p>
                  <p>
                    <strong>{__('Selected Posts:', 'html-social-share-buttons')}</strong>{' '}
                    {selectedPosts.length}
                  </p>
                  <p>
                    <strong>{__('Last Refreshed:', 'html-social-share-buttons')}</strong>{' '}
                    {shareCounts && shareCounts.length > 0
                      ? new Date().toLocaleString()
                      : __('Never', 'html-social-share-buttons')
                    }
                  </p>
                </div>
              </CardBody>
            </Card>
          </PanelBody>
        </Panel>
      </div>
    </div>
  )
}