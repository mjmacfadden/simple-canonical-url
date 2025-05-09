# Simple Canonical URL

A lightweight WordPress plugin that allows you to set custom canonical URLs for your posts and pages.

## Description

Simple Canonical URL gives you control over the canonical URLs of your WordPress content. It adds a user-friendly field to set canonical URLs in both the regular post editor and quick edit mode, helping you manage SEO for content that might appear in multiple locations.

### Features

- Set custom canonical URLs for posts and pages
- Edit canonical URLs from the regular post editor
- Edit canonical URLs directly from the posts list using quick edit
- Displays canonical URL in a column on the posts/pages list
- Automatically adds the proper canonical link tag to your page's HTML
- Overrides WordPress's default canonical URL when a custom one is specified

## Installation

1. Download the plugin zip file
2. Go to your WordPress admin panel → Plugins → Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Activate the plugin

Alternatively, you can manually install:

1. Download and unzip the plugin
2. Upload the `simple-canonical-url` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

### Setting a Canonical URL in Post Editor

1. Edit any post or page
2. Find the "Canonical URL" meta box (usually below the content editor)
3. Enter the full URL you want to set as canonical (e.g., `https://example.com/canonical-page/`)
4. Update or publish your post

### Setting a Canonical URL via Quick Edit

1. Go to Posts or Pages in your WordPress admin
2. Hover over a post/page and click "Quick Edit"
3. You'll see the Canonical URL field where you can enter or edit the URL
4. Click "Update"

### Viewing Canonical URLs

The plugin adds a "Canonical URL" column to your posts and pages list, making it easy to see which content has custom canonical URLs defined.

## FAQ

### What is a canonical URL?

A canonical URL helps search engines understand which URL is the "master" version when you have duplicate or similar content on multiple URLs. Setting a canonical URL prevents SEO issues related to duplicate content.

### Will this override WordPress's default canonical URLs?

Yes. When you set a custom canonical URL for a post or page, it will replace WordPress's default canonical URL output.

### What happens if I leave the canonical URL field empty?

If you don't specify a custom canonical URL, WordPress will use its default behavior for generating canonical URLs (typically using the post's permalink).

## Support

If you encounter any issues or have questions about this plugin, please open an issue on the GitHub repository or contact the plugin author.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0
* Initial release