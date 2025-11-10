# Running Routes

Плагин WordPress для визуализации треков GPX на интерактивных картах с поддержкой:

- Divi Builder (4 & 5)
- Gutenberg
- Classic Editor (shortcode + TinyMCE)
- ACF интеграция
- OpenLayers + OpenTopoMap / Mapy.cz

## Architecture

- Core: Route, Parser, Renderer
- Adapters: карты, форматы, интеграции
- Полностью расширяемый

running-routes/
├── running-routes.php                       ← главный файл
├── composer.json                            ← автозагрузка (опционально, пока нет такого файла)
├── package.json                             ← без зависимостей
├── .gitignore
├── README.md
├── assets/
│   ├── js/
│   │   ├── openlayers-map.js                ← рендер карты OpenLayers
│   │   └── admin.js                         ← заготовка (пусто) (нужен для TinyMCE, админка)
│   └── css/
│       └── frontend.css                     ← стили заглушки (временно)
├── core/
│   ├── interfaces/
│   │   ├── TrackParserInterface.php
│   │   └── MapRendererInterface.php
│   ├── formats/
│   │   └── GPXParser.php                    ← парсер GPX с вычислением дистанции
│   ├── Route.php                            ← абстракция маршрута
│   └── RouteManager.php                     ← CRUD, кэш, мета
├── includes/
│   ├── shortcodes.php
│   ├── widget.php                           ← стандартный WP_Widget
│   ├── admin/
│   │   ├── menu.php                         ← CPT раздел «Running Routes»
│   │   ├── gpx-upload.php                   ← метабокс для загрузки GPX-файла в «Running Routes»
│   │   └── views/                           ← не создана ???
│   └── modules/
│       └── RunningRoute/                    ← Divi 5 Extension (JSX + PHP). Не реализовано
│           ├── RunningRoute.jsx
│           ├── RunningRoute.php
│           ├── conversion-outline.json      ← это маппинг `divi4_shortcode_attr → divi5_prop`
│           └── module.json
├── integrations/
│   ├── classic-editor/
│   │   └── tinymce-button.js
│   ├── gutenberg/
│   │   ├── block.json
│   │   ├── index.js                         ← заготовка (пусто)
│   │   └── render.php                       ← заготовка (пусто)
│   ├── divi/
│   │   ├── legacy/                          ← Divi 4 Module
│   │   │   └── RunningRoutesDiviModule.php
│   │   └── extension/                       ← Не реализовано
│   │       └── divi5-module.php             ← заглушка, выводит шорткод (не подключен)
│   └── acf/                                 ← не создана ??? (хуки для ACF)
├── maps/                                    ← адаптеры карт
│   ├── OpenLayersRenderer.php               ← заготовка (пусто)
│   └── MapyCzRenderer.php                   ← заготовка (пусто)
├── templates/
│   └── single-running_route.php             ← шаблон
└── uninstall.php                            ← заготовка (пусто)

## Workflow

Этот проект использует [Gitflow workflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow):

- `main` — стабильная версия (только для релизов)
- `develop` — основная ветка разработки
- `feature/*` — новые функции
- `release/*` — подготовка релиза
- `hotfix/*` — срочные исправления
