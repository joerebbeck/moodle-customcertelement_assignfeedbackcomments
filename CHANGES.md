# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2026-03-19
### Added
- GitHub Actions Continuous Integration workflow running PHPUnit, Behat, PHPCS (Coding Style), and Linting against multiple PHP/Moodle/DB versions.
- Unit test coverage (`test_get_feedback_for_user_truncates_over_limit`) specifically validating correct HTML-aware truncation thresholds and append link generators.

### Changed
- Refactored feedback truncation logic to use `shorten_text()`, preserving safe inline formatting tags (e.g., bold, lists, paragraphs) instead of stripping all HTML.
- Updated `README.md` installation instructions with guides for uploading `.zip` plugin files via the Moodle Site Administration dashboard.
- Consolidated duplicate `json_decode()` operations in `get_feedback_for_user()` to improve performance.
- Adjusted plugin maturity from `MATURITY_STABLE` to `MATURITY_BETA` in `version.php` for release compliance.
- Cleaned up developer-specific inline notes inside `version.php`.

### Fixed
- Added missing `get_test_element()` method to solve fatal error crashes in the test suite.
- Corrected false-positive assertion expectation in the test suite.
- Removed dangling PHPDoc comment inside the test suite.
