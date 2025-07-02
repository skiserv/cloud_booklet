# Cloud Booklet

## Presentation

Theses small `PHP` scripts allow you to generate a booklet (PDF or website) from a content stored in the Cloud. This can be use for lyrics, poems or any booklet of small texts.

The website is a single page website that can also be installed in your device as a PWA (Progressive Web App).

## Configuration

A `config.php` file is needed at the root of the directory to use this project. Please copy following code, paste it in a new file and file each variable. 

```php
# config.php
<?php
class Config
{
    const BASE_URL = "";     # The root path of your cloud domain
    const KEY = "";          # The ID of the shared folder
    const PWD = "";          # The password of the shared folder

    const TITLE = "";        # The title of the website/PDF
    const SUBTITLE = "";     # The subtitle ...

# Some special pages that will be added in the header. Markdown file should start with _
    const PAGES = [
        "About" => "_about.md",
        "How to edit" => "_how_to_edit.md"
    ];

    const SUMMARY_TITLE = "";# The title of the summary (website page / PDF summary in first page)

    const LANG = "";         # The language of the content for website medatada [en | fr | ...]

    const PWA_THEME_COLOR = "";
    const PWA_BACKGROUND_COLOR = "";

    const LOGO_FILE = "";    # logo file path
    const LOGO_SIZE = "";    # eg `500x500`
    const LOGO_TYPE = "";    # eg `image/png`
}
```

For the PWA you should also add an icon as `icon.svg` with a size of `192x192`.

## Configuration

Made for PHP 8.1

## Credits

- [John Doe website](https://john-doe.neocities.org/) for the single page website tips.
- [Parsedown](https://github.com/erusev/parsedown/tree/1.7.4) markdown parser, under MIT license. Modified in two points (search for `# CHANGED` comments in code).
- [Paged.js](https://pagedjs.org/) for the print standards.