# Metrovalencia — Moodle Local Plugin

A Moodle local plugin that integrates Metrovalencia train information into Moodle, allowing users to check available stations and upcoming train forecasts.

This project is a **Minimum Viable Product (MVP)** developed to explore Moodle plugin development, external REST API integration, and Moodle's rendering architecture.

## Features

- **Station listing:** Retrieves available stations from the MetroAPI service.
- **Station selection:** Allows users to select a station from an alphabetically sorted dropdown.
- **Train forecasts:** Displays upcoming train information, including time, destination, line, and estimated remaining time.
- **Moodle rendering:** Uses Moodle's renderer system and Mustache templates.
- **Responsive interface:** Includes custom CSS for a clean, mobile-friendly layout.
- **Multilingual:** All user-facing text uses Moodle language strings (English and Spanish).
- **Access control:** Protected by the `local/metrovalencia:view` capability (granted to authenticated users by default).
- **Privacy API:** Implements a `null_provider`; the plugin stores no personal data.
- **Error handling:** Displays a user-friendly notification when station or forecast data cannot be retrieved.

## Technologies

- PHP
- Moodle Plugin API
- Mustache templates
- CSS
- REST API integration
- Git

## Requirements

- Moodle 5.0 or later (developed and tested on 5.3).
- Internet access to reach the MetroAPI service.

## Installation

1. Download or clone this repository.
2. Copy the `metrovalencia` directory into your Moodle installation's `local/` directory.
3. Log in as a site administrator.
4. Visit **Site administration → Notifications** to complete the plugin installation.
5. Purge caches (**Site administration → Development → Purge caches**) so Moodle picks up the stylesheet and language strings.
6. Open the plugin at `/local/metrovalencia/index.php`.

## API

This plugin uses the MetroAPI service to retrieve station information and train forecasts.

**API base URL:** https://metroapi.alexbadi.es

The API is an external dependency, so its availability and response format may affect the plugin's functionality.

## Project Structure

```text
local/metrovalencia/
├── classes/
│   ├── output/
│   │   ├── metrovalencia_page.php
│   │   └── renderer.php
│   ├── privacy/
│   │   └── provider.php
│   ├── hook_callbacks.php
│   ├── metrovalencia_api.php
│   └── page.php
├── db/
│   ├── access.php
│   └── hooks.php
├── lang/
│   ├── en/local_metrovalencia.php
│   └── es/local_metrovalencia.php
├── templates/
│   └── metrovalencia_page.mustache
├── CHANGELOG.md
├── README.md
├── index.php
├── styles.css
└── version.php
```

## Current Status

**MVP — v0.1.0 (alpha)**

The core functionality is implemented: retrieving stations, selecting a station, fetching train forecasts, and displaying the results within Moodle.

Potential future improvements are listed below.

### Future Improvements

- **Caching:** Use the Moodle Cache API to reduce external API requests (long TTL for stations, short TTL for forecasts).
- **Automated testing:** Add PHPUnit tests (the Moodle `curl` class can mock API responses).
- **Live refresh:** Add an AMD module and an external function so forecasts update without reloading the page.
- **Additional features:** Expand the plugin with new functionality.

## License

GNU General Public License v3 or later (GPL-3.0-or-later), following Moodle's plugin licensing conventions.
