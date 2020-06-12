# Built In Notifications

This module provides some default notifications that you can toggle on or off via the config.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"workflow": {
					"notifications": {
						"on-post-published": true,
						"on-submit-for-review": true,
						"on-update-assignees": true,
						"on-editorial-comment": true
					}
				}
			}
		}
	}
}
```

1. `on-post-published`: When a post is published the author and assignees are notified.
1. `on-submit-for-review`: When a new post is submitted for review the assignee and all editors are notified.
1. `on-update-assignees`: When a user is assigned to a post they are notified.
1. `on-editorial-comment`: When an editorial comment is added the assignees and post author are notified.

## Disabling notifications

If you do not wish to use the feature altogether you can set the `notifications` setting to false.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"workflow": {
					"notifications": false
				}
			}
		}
	}
}
```
