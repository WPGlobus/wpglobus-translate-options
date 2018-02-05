/**
 * @link https://www.npmjs.com/package/grunt-wp-readme-to-markdown
 */
module.exports = {
    options: {
        screenshot_url: "https://ps.w.org/wpglobus-translate-options/assets/{screenshot}.png"
    },
    main: {
        files: {
            "README.md": "readme.txt"
        }
    }
};
