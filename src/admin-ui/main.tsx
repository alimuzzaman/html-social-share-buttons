import React from 'react'
import ReactDOM from 'react-dom/client'
import { App } from './App'
import './index.css'

// Mount the React app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('hss-admin-react-root')

  if (container) {
    const root = ReactDOM.createRoot(container)
    root.render(
      <React.StrictMode>
        <App />
      </React.StrictMode>,
    )
  }
})

// Export for potential external use
export { App }