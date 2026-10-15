# Changelog

## 0.2.0 - 2026-10-16

### Added
- Moodle Cache API definitions (`db/caches.php`): station list cached for one day, forecasts cached for 20 seconds per station. Avoids waiting on MetroAPI on every page load.

### Changed
- MetroAPI request timeout raised from 5 s to 10 s (connection timeout stays at 3 s).

## 0.1.0 - 2026-10-15

### Added
- Capability `local/metrovalencia:view` and `require_capability()` check in `index.php`.
- Privacy API `null_provider`.
- Spanish language pack (`lang/es`).

### Changed
- All user-facing text moved to Moodle language strings.
- Template updated to Bootstrap 5 classes (`form-select`, `form-label`) and documented with a Mustache docblock.
- Stylesheet moved to `styles.css` in the plugin root so Moodle loads it automatically.
- Stations sorted with `core_collator` (locale-aware, handles accents).
- MetroAPI client now sets request and connection timeouts and checks cURL errors.
- Navigation entries only appear for users with the capability.
- MetroAPI User-Agent keeps the `contact=` e-mail, now taken from the Moodle site support address instead of a hardcoded `test@test.com`.
- Minimum Moodle version raised to 5.0 (required by the hooks used).
- Coding style aligned with Moodle coding standards; license headers added to all files.

### Removed
- Unused `metrovalencia_api::search_station()`.
- Leading blank lines before `<?php` in `version.php` and the language file.

## 0.0.1

- Initial MVP: station list, station selector and train forecasts.
