# wp-gb-primary-category
WP GB Primary Category plugin used to select the primary category for the single post. We have different scenarios where we add multiple categories but from that categories there is one category which is superior. To mark that particular category as primary category I have created a plugin that would create meta boxes (in classic editor) and plugin sidebar (in Gutenberg editor) and helps editor to set the primary category for that post. Below are some of the key features and filters you can use while using this plugin.

- There are plugins that are currently available like `Yoast SEO` which provide inbuilt marking for primary category. But this would only be useful while we are using SEO for site and also this plugin is very heavy for large sites.

## Features
- Add primary category box as meta in classic and Gutenberg editor.
- Classic Editor would have metabox while Gutenberg editor would as an Plugin Sidebar.
- Classic Editor would be having normal dropown selection while Gutenberg would be having AutoComplete as Combobox control to ease the editor experience (As now almost every WordPress site is moved and using Gutenberg for main editing purporse).

## Filters for custom support
- `wp_gb_add_cpt_support_meta` filter to add support for different post type for Gutenberg and Classic Editor Meta.
- `wp_gb_primary_category_filter_taxonomy` to filter the txonomy for Gutenberg support based on the post/cpt you view. Default would be `category` and `post_tag` Taxonomy.
- `wp_gb_primary_category_remove_from_post_type` to remove primary category meta from particular post type in Gutenberg.
- `wp_gb_add_taxonomies` to add taxonomies to create dynamic meta keys for different Taxonomy to add primary categories for same post.
## How to use
- Clone the repo inside the plugins folder and activate through the plugins page.
- Now head to any post and open in editor mode.
- For Gutenberg Editor you would see the Panel `Primary Categories Selection` and in that `Combobox Control` which you can use to select the primary category.
- For Classic Editor you would see the Panel `Primary Categories Selection` and `Select Controls` to select primary category.
