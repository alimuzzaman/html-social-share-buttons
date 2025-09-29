import { useState, useEffect } from 'react'
import apiFetch from '@wordpress/api-fetch'

interface ShareCountData {
  post_id: number
  title: string
  url: string
  share_counts: Record<string, number>
  total: number
  last_updated?: string
}

export const useShareCounts = (postIds: number[]) => {
  const [shareCounts, setShareCounts] = useState<ShareCountData[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const fetchShareCounts = async () => {
    if (postIds.length === 0) {
      setShareCounts([])
      return
    }

    setLoading(true)
    setError(null)

    try {
      const promises = postIds.map(async (postId) => {
        const response = await apiFetch({
          path: `/html-social-share/v1/posts/${postId}/share-counts`,
          method: 'GET',
        }) as any

        // Get post title
        const post = await apiFetch({
          path: `/wp/v2/posts/${postId}`,
          method: 'GET',
        }) as any

        return {
          post_id: postId,
          title: post.title.rendered,
          url: response.url,
          share_counts: response.share_counts,
          total: response.total,
          last_updated: response.last_updated,
        }
      })

      const results = await Promise.all(promises)
      setShareCounts(results)
    } catch (err) {
      console.error('Error fetching share counts:', err)
      setError('Failed to fetch share counts')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchShareCounts()
  }, [postIds])

  return {
    shareCounts,
    loading,
    error,
    refreshShareCounts: fetchShareCounts,
  }
}