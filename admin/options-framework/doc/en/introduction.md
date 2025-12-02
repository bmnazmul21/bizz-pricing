# Introduction

## What is BizzPlugin Options Framework?

BizzPlugin Options Framework is a modern, developer-friendly WordPress options framework designed to simplify the creation of settings panels for WordPress plugins and themes. It provides a clean, intuitive interface for both developers and end-users.

## Why Use This Framework?

### 1. Developer-Friendly

- **Minimal Code**: Create a complete settings panel with just a few lines of code
- **Chainable API**: Modern, fluent interface for building panels
- **Well-Documented**: Comprehensive documentation and examples
- **Type-Safe**: Proper data sanitization and validation built-in

### 2. Feature-Rich

- **20+ Field Types**: From simple text fields to complex image selectors
- **AJAX Save**: No page reloads needed when saving settings
- **REST API**: External integration support out of the box
- **Webhooks**: Notify external services when settings change
- **Export/Import**: Easy backup and restore of settings

### 3. Modern UI/UX

- **Responsive Design**: Works on all screen sizes
- **Intuitive Navigation**: Tab-based navigation with sections and subsections
- **Visual Feedback**: Clear save status indicators and notifications
- **Accessibility**: Built with WordPress accessibility standards

### 4. Extensible

- **WordPress Hooks**: Extensive filter and action hooks
- **Custom Field Types**: Add your own field types easily
- **Panel-Specific Filters**: Target specific panels without affecting others

## How It Works

The framework consists of two main components:

1. **Framework Class (`BizzPlugin_Framework`)**: Manages panels and handles AJAX/API requests
2. **Panel Class (`BizzPlugin_Panel`)**: Represents individual settings panels with sections and fields

### Basic Flow

```
Plugin → Creates Framework Instance → Creates Panel → Adds Sections & Fields → User Saves Settings → AJAX/Webhook/API
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Integration Methods

You can integrate the framework into your plugin in two ways:

### Method 1: Copy to Your Plugin

Copy the `options-framework` folder to your plugin directory and include the loader:

```php
require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
```

### Method 2: As a Dependency

If using Composer or a similar dependency manager, you can include the framework as a dependency.

## Next Steps

- [Getting Started](getting-started.md) - Set up your first settings panel
- [Field Types](field-types.md) - Explore available field types
- [Examples](examples.md) - See complete code examples
