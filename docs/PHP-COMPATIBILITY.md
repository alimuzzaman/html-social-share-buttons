# PHP compatibility

- **Minimum PHP:** 7.0
- **Current candidate metadata:** internally aligned at 3.0.0; this does not
  authorize a tag or release

The plugin header and Composer manifest both require PHP 7.0 or newer. The
canonical rewrite deliberately avoids typed properties, union types,
attributes, enums, readonly classes, and other syntax introduced after PHP 7.0.

## Evidence

The compatibility workflow defines syntax/bootstrap checks for PHP 7.0, 7.4,
8.0, 8.3, and 8.5. A fresh WordPress 5.3/PHP 7.0.33 instance activated the
exact candidate archive and rendered a saved legacy shortcode with a canonical
permalink. Modern PHPUnit and browser suites run on newer PHP because the
current test runner itself requires PHP 7.4 or newer.

The static compatibility contract also rejects implicitly nullable typed
parameters (`Type $value = null`), which PHP 8.4 deprecates, without introducing
post-PHP-7.0 syntax into distributed files.

This supports the declared runtime floor; it does not claim that the complete
modern test suite ran on PHP 7.0 or every intermediate PHP version.

## Compatibility rules

- Do not introduce post-PHP-7.0 syntax in distributed PHP files.
- Keep `composer.json`, the plugin header, and `readme.txt` aligned.
- Do not raise the minimum merely to clear a tooling warning; that is a release
  policy decision with user impact.
- Re-run the PHP matrix and exact support-floor activation whenever distributed
  PHP, Composer metadata, or the approved candidate archive changes.

See `RELEASE-CANDIDATE-VALIDATION.md` for dated command and archive evidence.
