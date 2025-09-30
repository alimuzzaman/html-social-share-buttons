import React, { useState, useEffect } from 'react'
import { __ } from '@wordpress/i18n'
import { ShareCountsTable } from './ShareCountsTable'
import { RefreshControls } from './RefreshControls'
import { useShareCounts } from '../hooks/useShareCounts'
import { useRestApi } from '../hooks/useRestApi'
import { LoadingSpinner, Notice } from './ui'

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
    <div className="hss-admin-interface max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">{__('Social Share Counts Manager', 'html-social-share-buttons')}</h1>
        <p className="text-gray-600 text-lg">{__('Manage and refresh social share counts across your site.', 'html-social-share-buttons')}</p>
      </div>

      {notification && (
        <div className="mb-6">
          <Notice type={notification.type} message={notification.message} dismissible onDismiss={clearNotification} />
        </div>
      )}

      <div className="space-y-6">
        <section className="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
          <h2 className="text-lg font-medium text-gray-800 mb-3">{__('Refresh Controls', 'html-social-share-buttons')}</h2>
          <RefreshControls
            selectedPosts={selectedPosts}
            onSelectionChange={setSelectedPosts}
            onBulkRefresh={handleBulkRefresh}
            refreshing={refreshing}
          />
        </section>

        <section className="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
          <h2 className="text-lg font-medium text-gray-800 mb-3">{__('Share Counts Data', 'html-social-share-buttons')}</h2>
          <div>
            {loading && (
              <div className="flex items-center justify-center py-8">
                <LoadingSpinner size="large" message={__('Loading share counts...', 'html-social-share-buttons')} />
              </div>
            )}

            {!loading && shareCounts && (
              <ShareCountsTable
                data={shareCounts}
                selectedPosts={selectedPosts}
                onSelectionChange={setSelectedPosts}
              />
            )}
          </div>
        </section>

        <section className="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
          <h2 className="text-lg font-medium text-gray-800 mb-3">{__('Statistics', 'html-social-share-buttons')}</h2>
          <div className="hss-stats space-y-2">
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
        </section>
      </div>
    </div>
  )
}