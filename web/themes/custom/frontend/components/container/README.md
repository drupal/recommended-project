# Container

## Component Properties

The Container component takes a variety of properties to customize its appearance and content:

- `list_group_html_tag` (string) (default: 'ul'): The HTML tag to use for the list group.
- `list_group_item_variants` (array) (default: []): An array of variants to apply to the list group items.
- `numbered` (bool) (default: false): Add numbers to the list group items.

## Usage:

```twig
{%
  include "frontend:container" with {
    container_size: 'small',
    container_bg_media: 'media',
    container_bg_color: 'bg_color',
    container_content: 'content'
  }
%}
```
