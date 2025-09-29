# Development and Testing Guide

## Test Environment Configuration

Your WordPress test site is configured at: **https://hssb.test/wp-admin/**

## Quick Start Testing

### 1. Run Comprehensive Tests
```bash
# Run all WordPress-native implementation tests
./build/run-tests.sh
```

### 2. Build and Test React Admin Interface
```bash
# Build the React admin interface
cd admin-ui
npm install
npm run build

# Test in browser
# Navigate to: https://hssb.test/wp-admin/admin.php?page=html-social-share-react
```

### 3. Test REST API Endpoints
```bash
# Test with WP-CLI
wp eval-file test-rest-api.php --url=https://hssb.test

# Or test with curl
curl -X GET "https://hssb.test/wp-json/html-social-share/v1/posts/1/share-counts" \
  -H "Content-Type: application/json"
```

### 4. Run Playwright UI Tests
```bash
# Test both classic and React admin interfaces
node test-settings-playwright.js
```

## Development Workflow

### For WordPress Backend Development
1. **Service Registration**: Add new services in `src/Bootstrap/ServiceRegistrar.php`
2. **Hook Registration**: Wire hooks in `src/Bootstrap/HookRegistrar.php`
3. **Database Operations**: Use `$wpdb` with prepared statements
4. **Caching**: Leverage `WordPressCache` for transient storage
5. **Testing**: Run `./build/run-tests.sh` after changes

### For React Frontend Development
1. **Development Mode**:
   ```bash
   cd admin-ui
   npm run dev  # Vite dev server
   ```

2. **Production Build**:
   ```bash
   npm run build
   ```

3. **Type Checking**:
   ```bash
   npm run type-check
   ```

## Available Test Endpoints

### REST API Endpoints
- `GET /wp-json/html-social-share/v1/posts/{id}/share-counts`
- `GET /wp-json/html-social-share/v1/share-counts?url={url}`
- `PUT /wp-json/html-social-share/v1/posts/{id}/share-counts/{network}`
- `POST /wp-json/html-social-share/v1/posts/{id}/share-counts/refresh`
- `POST /wp-json/html-social-share/v1/share-counts/bulk-refresh`
- `DELETE /wp-json/html-social-share/v1/posts/{id}/share-counts`

### Admin Pages
- **Classic Admin**: `https://hssb.test/wp-admin/admin.php?page=html-social-share`
- **React Admin**: `https://hssb.test/wp-admin/admin.php?page=html-social-share-react`

## WordPress Integration Testing

### Manual Testing Checklist

#### ✅ WordPress Native Features
- [ ] Transient caching works (`get_transient`, `set_transient`)
- [ ] Database operations use prepared statements
- [ ] Proper capability checking (`current_user_can`)
- [ ] Nonce verification for security
- [ ] REST API endpoints respond correctly

#### ✅ React Admin Interface
- [ ] Admin page loads without errors
- [ ] React components render properly
- [ ] API calls to REST endpoints work
- [ ] Bulk operations function correctly
- [ ] UI is responsive and accessible

#### ✅ Performance
- [ ] Cache operations are fast (< 100ms typical)
- [ ] Database queries are optimized
- [ ] React app loads quickly
- [ ] No memory leaks in long-running operations

## Debugging Tools

### WordPress Debug Mode
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### PHP Error Checking
```bash
# Check syntax of all PHP files
find src -name "*.php" -exec php -l {} \;

# Check specific file
php -l src/WordPressCache.php
```

### REST API Testing
```bash
# Install WP-CLI REST command
wp package install wp-cli/restful

# Test endpoints
wp rest list html-social-share/v1 --url=https://hssb.test
```

### Database Inspection
```bash
# Check table structure
wp db query "DESCRIBE wp_hss_share_counts" --url=https://hssb.test

# View sample data
wp db query "SELECT * FROM wp_hss_share_counts LIMIT 5" --url=https://hssb.test
```

## Troubleshooting Common Issues

### Issue: REST API 404 Errors
**Solution**: Flush rewrite rules
```bash
wp rewrite flush --url=https://hssb.test
```

### Issue: React App Not Loading
**Solutions**:
1. Build the React app: `cd admin-ui && npm run build`
2. Check file permissions on built assets
3. Verify WordPress can serve static files from the plugin directory

### Issue: Database Errors
**Solutions**:
1. Check database permissions
2. Verify table exists: `wp db query "SHOW TABLES LIKE '%hss%'" --url=https://hssb.test`
3. Run activation hook: `wp eval 'do_action("activate_html-social-share/html-social-share.php");' --url=https://hssb.test`

### Issue: Cache Not Working
**Solutions**:
1. Check WordPress transients: `wp transient list --url=https://hssb.test`
2. Verify database write permissions
3. Test manual transient operations

### Issue: Permission Errors
**Solutions**:
1. Check user capabilities: `wp user list --fields=user_login,roles --url=https://hssb.test`
2. Verify REST API authentication
3. Check nonce verification

## Next Development Steps

### Immediate (Ready for Implementation)
1. **Complete React Build System**: Finalize Vite configuration and build process
2. **Enhanced Error Handling**: Add comprehensive error logging and user feedback
3. **Performance Optimization**: Implement query caching and batch operations
4. **Security Hardening**: Add rate limiting and input validation

### Short Term (1-2 weeks)
1. **Advanced Admin Features**:
   - Share count analytics and reporting
   - Scheduled refresh configuration
   - Network API key management
   - Export/import functionality

2. **Integration Extensions**:
   - WooCommerce product sharing
   - Custom post type support
   - Third-party plugin hooks
   - Multisite compatibility

### Long Term (1+ months)
1. **Performance Analytics**: Detailed sharing performance tracking
2. **A/B Testing**: Different button styles and placements
3. **Advanced Caching**: Redis/Memcached integration
4. **Mobile App API**: REST API extensions for mobile apps

## File Organization

```
html-social-share-buttons/
├── src/                          # PHP backend
│   ├── WordPressCache.php        # WordPress transient cache
│   ├── REST/                     # REST API endpoints
│   ├── Admin/                    # Admin interfaces
│   └── Bootstrap/                # Service registration
├── admin-ui/                    # React frontend
│   ├── src/                      # React components
│   ├── package.json              # Dependencies
│   └── vite.config.ts           # Build configuration
├── test-rest-api.php            # WordPress API tests
├── build/run-tests.sh            # Comprehensive test suite
├── test-settings-playwright.js  # UI automation tests
└── docs/                        # Documentation
```

This guide provides a complete framework for continuing development and testing of the WordPress-native implementation. All components are designed to work together seamlessly while maintaining WordPress standards and best practices.