# Notifications

The key to workflows is receiving timely notifications in the places where you work. This module provides a developer framework for creating notifications, either as hardcoded business logic or by making events, messages, recipients and destinations available to the Workflow builder UI.

This component is powered by the [Workflows plugin](https://github.com/humanmade/Workflows). You can find more [detailed documentation on the plugin's wiki](https://github.com/humanmade/Workflows/wiki).

## Hardcoded notifications

Notifications can be defined in code to ensure consistency and reduce manual effort when setting up an application's business logic. They will not be visible in the admin UI.

The simplest form of a notification defined in code looks like the following:

```php
HM\Workflows\Workflow::register( 'notify_editor_on_new_post' )
  // The WordPress action hook to trigger on.
  // For post status transitions follow the pattern `<old-status>_to_<new-status>`.
  ->when( 'draft_to_pending' )
  // The message to be sent. Can also be a callback that receives data from the action hook.
  ->what( 'A new post is ready for review' )
  // The user role to notify. Could also be an array of user IDs.
  ->who( 'editor' )
  // Use the 'email' destination. This method can be used multiple times for multiple destinations.
  ->where( 'email' );
```

The `Workflow` object answers the question "when" should the action happen, "what" should be sent, "who" should receive the message and finally "where" should they receive it.

**Note:** this notification could also be set up manually in the admin.

## Adding a custom event to the UI

`Event`s control when a notification is triggered and how the data passed back from the action hook or callback should be interpreted.

To extend the options available to users for constructing custom workflows in the CMS admin you can register `Event` objects and define their UI.

The following example shows the addition of an `Event` trigger that listens on the `publish_post` action hook and sets up message tags, follow up message actions and a custom recipient handler.

```php
HM\Workflows\Event::register( 'publish_post_event' )
  // Set the action hook that event will fire on. Event listeners are
  // very customisable and can be arbitrary callbacks, you can even
  // pre-process the data returned from an action hook to conditionally
  // trigger the event or customise the returned data.
  ->set_listener( 'publish_post' )
  // Message tags are used to provide dynamic placeholders like `%title%`
  // that are replaced in the message's subject or body text.
  ->set_message_tags( [
	  'title' => function ( $post_id ) {
		  return get_the_title( $post_id );
	  }
  ] )
  // Add a custom recipient handler that uses data from the event's
  // action hook to allow selecting the post's author as a recipient
  // of the notification.
  ->add_recipient_handler( 'author', function ( $post_id ) {
	  return get_user_by( 'id', get_post( $post_id )->post_author );
  }, __( 'Post author' ) )
  // Add a link to the messages that are sent out. The callback can
  // return a URL to redirect the user to but can also perform any
  // actions the user clicking the link has permission to do.
  ->add_message_action(
    'view',
    __( 'View post' ),
    function ( $post_id ) {
      return get_the_permalink( $post_id );
    },
    function ( $post_id ) {
      return [ 'post_id' => $post_id ];
    },
    [ 'post_id' => 'intval' ]
  )
  // Finally add a UI, this causes the event to show in the dropdown
  // of events in the admin.
  ->add_ui( __( 'When a post is published' ) );
```

Message actions are very powerful and operate via a webhooks API under the hood. [You can learn more about the possible uses of message actions here](https://github.com/humanmade/Workflows/wiki/Event#add_message_action-string-id-string-text-stringcallable-callback_or_url-arraynull-args--null-array-schema---array-data-----event).

Some other example uses of message actions may be for "jumping to editing a post", "providing a preview link", "publishing a post" and anything else you can think of. The links will work from any location whether in the CMS admin, an email or a slack message.

## Adding a custom destination to the UI

Destinations handle dispatching the notifications. Email, dashboard and [Slack](https://slack.com/) notifications are supported by default.

The following example shows the registration of a custom destination handler that posts data to an external API using data entered via the UI.

You can create any number of integrations with external services through this method.

**Note** the destination handler is run as a background task to prevent slowing down the script execution.

```php
HM\Workflows\Destination::register( 'custom-api', function ( array $recipients, array $message, array $ui_data = [] ) {
	// Check we have an auth token set in the UI form field.
	if ( ! $ui_data['auth_token'] ?? false ) {
		return;
	}

	// Hit the API with the message for each user.
	foreach ( $recipients as $recipient ) {
		// Build up some data to send to the API. $recipient is a WP_User object.
		$data = [
			'name' => $recipient->get( 'display_name' ),
			'id' => $recipient->get( 'custom_api_id' ),
			'subject' => $message['subject'],
			'text' => $message['text'],
			'links' => $message['actions'],
		];

		// Post the data.
		wp_remote_post( 'https://custom-api.example.com/notify', [
			'headers' => [
				'Content-type' => 'application/json',
				'Authorization' => 'bearer ' . $ui_data['auth_token'],
			],
			'body' => json_encode( $data ),
		] );
	}
} )
  // Add the destination to the admin UI with the following label.
  ->add_ui( __( 'Custom API Notifications' ) )
  // Retrieve the UI object.
  ->get_ui()
    // Add a text field to the UI for entering an authentication token.
    ->add_field( 'auth_token', __( 'Auth Token' ), 'text', [
		'description' => __( 'Enter your API authentication token here' ),
	] );
```
