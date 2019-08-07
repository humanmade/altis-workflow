# Editorial Comments

Discussions relating to a piece of content provide useful context and a shared understanding of what has been done and what is still to do for a piece of content.

This feature along with the ability to assign posts to individuals helps to speed up the writing process by notifying assignees and providing links to follow up quickly.

## Post type support

By default posts, pages and media have support for editorial comments. You can add and remove support for these for specific post types using the post type support flag `editorial-comments`.

Below is an example add support for editorial comments to an example custom post type called `event`:

```php
add_action( 'init', function () {
	add_post_type_support( 'event', 'editorial-comments' );
} );
```

The following example removes editorial comments from pages and media.

```php
add_action( 'init', function () {
	remove_post_type_support( 'page', 'editorial-comments' );
	remove_post_type_support( 'attachment', 'editorial-comments' );
} );
```

Support can also be added when registering a post type by adding `editorial-comments` to the `supports` array in the post type arguments array.

```php
register_post_type( 'event', [
	'label' => __( 'Events' ),
	'public' => true,
	'supports' => [
		'title',
		'editor',
		'editorial-comments',
	]
	// ... etc ...
] );
```
