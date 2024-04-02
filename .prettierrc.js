module.exports = {
	...require( '@wordpress/prettier-config' ),
	overrides: [
		{
			files: [ '*.css', '*.js', '*.jsx', '*.ts' ],
			options: {
				useTabs: true,
				printWidth: 80,
				singleQuote: true,
				trailingComma: 'es5',
				bracketSpacing: true,
				parenSpacing: true,
				bracketSameLine: false,
				semi: true,
				arrowParens: 'always',
			},
		},
	],
};
