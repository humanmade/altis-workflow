# Default Workflows

This module provides some default workflows that you can toggle on or off via the config.

## Post publishing

1. When a new post is submitted for review the assignee and all editors are notified.
2. When a post is published the author and assignees are notified.

## Assignments and Editorial comments

1. When an editorial comment is added the assignees and post author are notified.
2. When a user is assigned to a post they are notified.

## Configuring default workflows

You can disable the default workflows using any of the following configuration options:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"workflow": {
					"posts-workflow": false,
					"editorial-workflow": false
				}
			}
		}
	}
}
```
