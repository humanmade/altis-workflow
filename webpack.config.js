const { helpers, externals, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;

module.exports = presets.production( {
	externals,
	entry: {
		"republish-strings": filePath( 'assets/republish-strings.js' ),
		"register-amend-post": filePath( 'assets/register-amend-post.js' ),
	},
	output: {
		path: filePath( 'build' ),
	},
} );
