# Mage_ConfigSearch

## Overview

Adds a dedicated search input directly on the Magento Admin **System > Configuration** page, allowing administrators to instantly find any configuration field across all system.xml definitions.

## Purpose

Magento's built-in global search (magnifying glass in header) provides generic results across multiple entity types. This module provides a **focused, scope-aware configuration search** embedded in the config page itself — between the scope switcher and Save Config button — that stays visible in the sticky header when scrolling.

## Benefits

- **Instant field discovery** — Search across all tabs, sections, groups, and fields by label with real-time AJAX results
- **Scope-aware filtering** — Results automatically respect `showInDefault`, `showInWebsite`, and `showInStore` visibility based on the currently selected scope
- **Color-coded breadcrumbs** — Each result shows a full path (Tab > Section > Group > Field) with color-coded segments for quick visual identification
- **Direct navigation** — Click any result to navigate directly to the correct configuration section
- **Sticky header support** — Search input remains accessible in the floating header when scrolling through long configuration pages
- **Keyboard navigation** — Navigate results with arrow keys, select with Enter, dismiss with Escape
