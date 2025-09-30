import apiFetch from '@wordpress/api-fetch'

interface BulkRefreshResult {
  success: boolean
  processed: number
  errors: number
  results: Array<{
    post_id: number
    url: string
    success: boolean
    refreshed_networks: string[]
  }>
  error_details: Array<{
    post_id: number
    error: string
  }>
  refreshed_at: string
}

export const useRestApi = () => {
  const bulkRefresh = async (postIds: number[], networks?: string[]): Promise<BulkRefreshResult> => {
    const response = await apiFetch({
      path: '/html-social-share/v1/share-counts/bulk-refresh',
      method: 'POST',
      data: {
        post_ids: postIds,
        networks: networks || [],
      },
    }) as BulkRefreshResult

    return response
  }

  const refreshSinglePost = async (postId: number, networks?: string[]): Promise<any> => {
    const response = await apiFetch({
      path: `/html-social-share/v1/posts/${postId}/share-counts/refresh`,
      method: 'POST',
      data: {
        networks: networks || [],
      },
    })

    return response
  }

  const updateShareCount = async (postId: number, network: string, count: number): Promise<any> => {
    const response = await apiFetch({
      path: `/html-social-share/v1/posts/${postId}/share-counts/${network}`,
      method: 'PUT',
      data: {
        count,
      },
    })

    return response
  }

  const deletePostShareCounts = async (postId: number): Promise<any> => {
    const response = await apiFetch({
      path: `/html-social-share/v1/posts/${postId}/share-counts`,
      method: 'DELETE',
    })

    return response
  }

  return {
    bulkRefresh,
    refreshSinglePost,
    updateShareCount,
    deletePostShareCounts,
  }
}