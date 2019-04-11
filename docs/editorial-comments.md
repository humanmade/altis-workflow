# Editorial Comments

Discussions relating to a piece of content provide useful context and a shared understanding of what has been done and what's is still to do for a piece of content.

This feature along with the ability to assign posts to individuals helps to speed up the writing process by notifying assignees and providing links to follow up quickly.

<!--
## REST API

Editorial comments are managed via the REST API. You can find the full API documentation here.
-->

## Disabling editorial comments

By default posts, pages and media have support for editorial comments. You can add and remove support for these for specific post types using the post type support flag `editorial-comments`.

The following example removes editorial comments from pages and media.

```php
add_action( 'init', function () {
	remove_post_type_support( 'page', 'editorial-comments' );
	remove_post_type_support( 'attachment', 'editorial-comments' );
} );
```
