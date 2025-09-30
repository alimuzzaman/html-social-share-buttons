import React from 'react'
import ReactDOM from 'react-dom/client'
import { MainAdminInterface } from './components/MainAdminInterface'
import './index.css'

// Mount the React app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('hss-admin-react-root')

  if (container) {
    const root = ReactDOM.createRoot(container)
    root.render(
      <React.StrictMode>
        <MainAdminInterface />
      </React.StrictMode>,
    )
  }
})

// Export for potential external use
export { MainAdminInterface }