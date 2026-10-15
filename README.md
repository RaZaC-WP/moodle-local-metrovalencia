# Metrovalencia — Moodle Local Plugin

A Moodle local plugin that integrates Metrovalencia train information into Moodle, allowing users to check available stations and upcoming train forecasts.

This project is a **Minimum Viable Product (MVP)** developed to explore Moodle plugin development, external REST API integration, and Moodle's rendering architecture.

## Features

- **Station listing:** Retrieves available stations from the MetroAPI service.
- **Station selection:** Allows users to select a station from an alphabetically sorted dropdown.
- **Train forecasts:** Displays upcoming train information, including time, destination, line, and estimated remaining time.
- **Moodle rendering:** Uses Moodle's renderer system and Mustache templates.
- **Responsive interface:** Includes custom CSS for a clean, mobile-friendly layout.
- **Error handling:** Displays a user-friendly notification when station or forecast data cannot be retrieved.

## Technologies

- PHP
- Moodle Plugin API
- Mustache templates
- CSS
- REST API integration
- Git

## Requirements

- Moodle 5.3 (development environment).
- Internet access to reach the MetroAPI service.

## Installation

1. Download or clone this repository.
2. Copy the `metrovalencia` directory into your Moodle installation's `local/` directory.
3. Log in as a site administrator.
4. Visit **Site administration → Notifications** to complete the plugin installation.
5. Open the plugin at `/local/metrovalencia/index.php`.

## API

This plugin uses the MetroAPI service to retrieve station information and train forecasts.

**API base URL:** https://metroapi.alexbadi.es

The API is an external dependency, so its availability and response format may affect the plugin's functionality.

## Project Structure

```text
local/metrovalencia/
├── assets/
│   ├── css/
│   └── styles.css
├── classes/
│   ├── metrovalencia_api.php
│   ├── page.php
│   └── output/
│       ├── metrovalencia_page.php
│       └── renderer.php
├── lang/
│   └── en/
│       └── local_metrovalencia.php
├── templates/
│   └── metrovalencia_page.mustache
├── index.php
├── version.php
└── README.md
```

## Current Status

**MVP — Initial implementation**

The core functionality is implemented: retrieving stations, selecting a station, fetching train forecasts, and displaying the results within Moodle.

Potential future improvements include automated tests, caching, and additional interface features.

### Future Improvements

- **Multilingual support:** Move user-facing text into Moodle language strings, keeping the English strings in `lang/en/` and adding their Spanish equivalents in `lang/es/`.
- **Automated testing:** Add unit and integration tests.
- **Caching:** Reduce external API requests and improve performance.
- **Additional features:** Expand the plugin with new functionality.

## License

GNU General Public License v3 or later (GPL-3.0-or-later), following Moodle's plugin licensing conventions.