const { helpers, externals, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;

module.exports = presets.production( {
	externals,
	entry: {
		scripts: filePath( 'assets/scripts.js' ),
	},
	output: {
		path: filePath( 'build' ),
	},
} );
