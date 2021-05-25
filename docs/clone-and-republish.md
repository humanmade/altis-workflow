# Clone & Republish

The Clone & Republish feature of Altis adds two separate, powerful features that work together to improve your content management workflows.

This feature is enabled by default, but can be disabled by editing the configuration file:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"workflow": {
					"clone-republish": false
				}
			}
		}
	}
}
```

![Quick Action row options](./assets/duplicate-post-list.png)

## Post Cloning

This feature adds a link to the post list screen that allows a user to clone a post with its metadata and terms to a new, identical post. This duplicate post will automatically link back to the original post but this reference can be removed by editing the post and checking the "Delete reference to original item" checkbox in the Duplicate Post panel in the editor.

![Duplicate post delete reference panel](./assets/duplicate-post-delete-reference.png)

## New Draft

Posts can have multiple concurrent drafts with the New Draft feature. Clicking New Draft will create a new duplicate copy of the original post that can be edited independently of the original. Publishing a post that was created with the New Draft works the same as publishing any other new post, with the exception that the original post will be linked from the New Draft copy in the admin. This reference is functionally the same as the reference in a cloned post.

## Rewrite & Republish

Posts that have been published have the ability to be edited, saved as a draft, revised and republished without affecting the published post with the Rewrite & Republish link. This link does not appear on posts that are drafts or are themselves clones or republished posts. In other words, while you may click Rewrite & Republish to create multiple draft copies of an originating post, you cannot Rewrite & Republish a post that is a copy of that post.

When you click the link to Rewrite & Republish a post, a cloned version of that post is created for you to edit. When you have completed your edits and are ready to publish, the content from the updated copy _replaces_ the original post content and the duplicate is deleted. In this way, it is unique from the Clone and New Draft features which can create a copy but do not replace the original.

## Enabled Post Types

The post cloning, new draft and rewrite & republish features are enabled for `post` and `page` post types by default. You can modify this behavior by passing the post types you want to enable these features on to the `altis.modules.workflow.clone-republish.post-types` configuration option.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"workflow": {
					"clone-republish": {
						"post-types": [ 'page', 'product' ]
					}
				}
			}
		}
	}
}
```

The example above enables the Clone & Republish features on the `page` and `product` post types only. In this case, those features would _not_ be available for the `post` post type.

## Developer Documentation

For full documentation on available [template tags](https://developer.yoast.com/duplicate-post/functions-template-tags) and [action and filter hooks](https://developer.yoast.com/duplicate-post/filters-actions), go to the [Yoast Duplicate Post developer documentation site](https://developer.yoast.com/duplicate-post/overview).