#!/bin/bash

# Build script for HTML Social Share Buttons React Admin Interface
set -e

echo "🔨 Building React Admin Interface..."

# Navigate to React directory
cd admin-ui

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Build the React app
echo "⚡ Building React app..."
npm run build

# Check if build was successful
if [ -d "dist" ]; then
    echo "✅ Build successful! Files created in assets/js/admin-ui/"
    echo "📁 Built files:"
    ls -la assets/js/admin-ui/
else
    echo "❌ Build failed!"
    exit 1
fi

echo "🎉 React Admin Interface build complete!"