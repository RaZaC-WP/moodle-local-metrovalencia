# Metrovalencia — Moodle Local Plugin

A Moodle local plugin that shows **Metrovalencia stations and real-time train forecasts** without leaving the learning platform. Pick a station and see the next trains: time, destination, line and the time remaining.

> Unofficial project, not affiliated with Metrovalencia or FGV. Train data comes from a third-party API (see [API](#api)).

<!-- TO DO Add a screenshot here, e.g. ![Metrovalencia page](docs/screenshot.png) -->

## About the project

This is a **Minimum Viable Product (MVP)** built as a portfolio piece to practise Moodle plugin development end to end: integrating an external REST API, rendering with Moodle's own architecture, and following Moodle's conventions for security, privacy, caching and internationalisation.

It is intentionally small, but it is structured the way a production Moodle plugin should be: no business logic in the template, no hardcoded strings, permissions enforced through capabilities, and the external API isolated behind a single class.

## Features

- **Station listing:** retrieves available stations from MetroAPI and shows them in an alphabetically sorted dropdown (locale-aware, so accented names sort correctly).
- **Train forecasts:** upcoming trains with time, destination, line and estimated time remaining.
- **Moodle rendering:** renderable/templatable classes, a renderer and a Mustache template (Bootstrap 5 markup).
- **Caching:** Moodle Cache API. Stations are cached for 1 day and forecasts for 20 seconds per station, so repeated page loads do not wait on the external API.
- **Navigation integration:** entries in the primary navigation and the user menu through Moodle's Hooks API.
- **Access control:** protected by the `local/metrovalencia:view` capability (granted to authenticated users by default).
- **Multilingual:** all user-facing text uses Moodle language strings (English and Spanish).
- **Privacy API:** implements a `null_provider`; the plugin stores no personal data.
- **Resilient API client:** request and connection timeouts, cURL/HTTP/JSON validation, and a user-friendly notification when data cannot be retrieved.

## How it works

1. The user opens `/local/metrovalencia/index.php`. The page requires login and the `local/metrovalencia:view` capability.
2. `page::render()` loads the station list (cache first, MetroAPI otherwise) and builds the sorted dropdown options.
3. The requested `stationid` is validated against that list. An invalid or missing value falls back to the first station, so arbitrary IDs never reach the API.
4. The forecasts for that station are loaded (cache first, MetroAPI otherwise).
5. The data is wrapped in `metrovalencia_page` (a `templatable`), rendered by the plugin renderer and displayed through the Mustache template.
6. If anything fails, the error is logged with `debugging()` (developer level) and the user sees a notification instead of a broken page.

## Architecture

| File | Responsibility |
| --- | --- |
| `index.php` | Entry point: login, capability check and page setup. |
| `classes/page.php` | Orchestrates the request: loads data, validates input, renders or shows an error. |
| `classes/metrovalencia_api.php` | The only class that talks to MetroAPI. Handles HTTP, timeouts, JSON validation and caching. |
| `classes/output/metrovalencia_page.php` | Prepares the data for the template (`renderable` + `templatable`). |
| `classes/output/renderer.php` | Renders the page from the Mustache template. |
| `classes/hook_callbacks.php` | Adds the navigation and user menu entries through the Hooks API. |
| `classes/privacy/provider.php` | Privacy API `null_provider`. |
| `db/access.php` | Capability definition. |
| `db/caches.php` | Cache definitions (stations and forecasts). |
| `db/hooks.php` | Hook callback registration. |
| `lang/` | English and Spanish language strings. |
| `templates/metrovalencia_page.mustache` | Page markup. |
| `styles.css` | Plugin styles (loaded automatically by Moodle). |

## Technologies

PHP · Moodle Plugin API (Hooks, Cache, Privacy, Capabilities) · Mustache · CSS · REST API integration · Git

## Requirements

- Moodle 5.0 or later (developed and tested on 5.3).
- Outbound internet access from the Moodle server to reach MetroAPI.

## Installation

1. Download or clone this repository.
2. Copy the `metrovalencia` directory into your Moodle installation's `local/` directory.
3. Log in as a site administrator.
4. Visit **Site administration → Notifications** to complete the plugin installation (this also registers the capability and the caches).
5. Purge caches (**Site administration → Development → Purge caches**) so Moodle picks up the stylesheet and language strings.
6. Open the plugin at `/local/metrovalencia/index.php`, or use the **Metrovalencia** entry in the navigation.

## API

The plugin uses the community **MetroAPI** service to retrieve station information and train forecasts.

**API base URL:** https://metroapi.alexbadi.es

MetroAPI asks clients to identify themselves with a contact e-mail, so the plugin sends it in the `User-Agent` header, taken from the **site support e-mail** configured in Moodle. Make sure it is set to an address you control.

The API is an external dependency, so its availability, speed and response format affect the plugin. Caching and timeouts are there to limit that impact.

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
│   ├── caches.php
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

**MVP — v0.2.0 (alpha)**

The core functionality is complete: retrieving stations, selecting a station, fetching train forecasts and displaying them within Moodle, with caching, permissions, privacy and translations in place. See the [CHANGELOG](CHANGELOG.md) for details.

### Future Improvements

- **Automated testing:** add PHPUnit tests (Moodle's `curl` class can mock API responses).
- **Live refresh:** add an AMD module and an external function so forecasts and the countdown update without reloading the page.
- **Favourite stations:** store a preferred station per user (this would require updating the privacy provider).

## License

GNU General Public License v3 or later (GPL-3.0-or-later), following Moodle's plugin licensing conventions.
