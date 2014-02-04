# Fabric

Fabric is a lightweight WordPress stack for creating better custom themes in less time.

## Attribution
Built by Matt Keys @ [UpTrending](http://uptrending.com)

## Features

### Activation & Packages

After activating Fabric you will be automatically redirected to the Theme Customizer. the theme customizer is utilized to setup some basic WordPress settings and also to choose plugin packages. Changes made in the theme customizer will be applied after clicking the save button at the top.

##### Plugin Packages

Plugin packages are designed to 'quick start' your new WordPress build by allowing you to assemble a list of your commonly used plugins for automatic installation and activation. Packages are located in lib/packages and are written using [YAML](http://www.yaml.org/). See the bundled package below:

```yaml
Common:
    description: A selection of commonly used plugins
    plugins:
        WP Scaffolding:
            source: wp-scaffolding.zip
            description: A powerful tool designed to make working with Fabric easier. Create CPTs; Taxonomies; Controllers and Views

        Advanced Custom Fields:
            source: http://wordpress.org/plugins/advanced-custom-fields/
            description: Fully customise WordPress edit screens with powerful fields. Boasting a professional interface and a powerfull API

        Root Relative URLs:
            source: http://wordpress.org/plugins/root-relative-urls/
            description: Converts all URLs to root-relative URLs for hosting the same site on multiple IPs; easier production migration and better mobile device testing.

        Redirection:
            source: http://wordpress.org/plugins/redirection/
            description: Manage 301 redirections and keep track of 404 errors.

        Quick Cache:
            source: http://wordpress.org/plugins/quick-cache/
            description: Quick Cache provides reliable page caching for WordPress.

The above example will allow the 5 listed plugins to be chosen for automatic installation during activation. Note the source field for each plugin. Plugins hosted on the WordPress respository will automatically be downloaded and installed from WordPress. If you have any plugins that are not on the WordPress repository, you can place them in lib/packages/local_plugins/. If you are installing a local plugin, the source for that plugin in your package file should be the name of the file including the .zip extension.

You can create multiple package files to organize your plugins.

##### Permalinks & Static Home Page

1. ***Reset permalink structure***: Change permalinks to /%postname%/
2. ***Enabled Nice Search?***: Redirect /?s= to /search/
3. ***Front page display***: Configure the front page to show a static page or your latest posts

##### Create and Set Navigation Menu

Fabric comes preconfigured to support a navigation menu called "Primary Navigation", checking the "Create Primary Navigation Menu" option in this section will generate the menu in the Menu area of WordPress and assign the proper menu location.

#### Template Redirection

Fabric moves all templates (called views in fabric) into a subfolder within the theme directory. Moving the views into a subfolder keeps your theme directory more organized.

Template redirection happens very early on in WordPress, before the theme is actually setup and read by WordPress. This is accomplished using a "must use" plugin in the /wp-content/mu-plugins directory called "FabricTemplateRedirection.php".

All the plugin does is filter the output of [get_template_directory](http://codex.wordpress.org/Function_Reference/get_template_directory). However this must be done in a mu-plugin because only mu-plugins are read early enough in the execution of WordPress to add our filter before get_template_directory() is called.

The template redirection will attempt to auto-install itself on theme activation, and remove itself if Fabric is deactivated. If WordPress cannot write to the wp-content directory, you will need to copy the FabricTemplateRedirection.php file from /lib/activation and place it into the wp-content/mu-plugins directory (creating the folder if it does not exist). Template redirection is designed to not interfere with other themes in the event that it is somehow left in the mu-plugins folder after switching themes, however it should still be removed if not in use.

### Controller Hierarchy & Views

Fabric uses controllers and views to keep your code more organized and easy to read. Your HTML/Presentational code goes into the "views" folder, while your logic goes into the "controllers" folder.

Fabric automatically determines and includes the correct controller to load based on the requested page. This controller hierarchy is modeled off of the WordPress [Template Hierarchy](http://codex.wordpress.org/Template_Hierarchy).

When working inside of your views, you can access functions or public variables from your Controllers by using $view. For instance, if you have the following function in your controller:

```php
public function company_info()
{
	echo get_option( 'my_company_info' );
}

You could use that function in your view like so:

```php
$view->company_info();


##### Base Controller

All other controllers extend from the base controller. Place any logic here that you would like to be available everywhere. Some default functionality is included in the base controller such as:

***config()***: This file is where we add theme support for menus & post thumbnails, register our primary navigation, and define the length of our post excerpts.

***sidebar_blacklist()***: An array of boolean conditionals (see [WordPress Conditionals](http://codex.wordpress.org/Conditional_Tags)). This is one of two ways to control the display (or not) of the sidebar on your pages.

***title_blacklist()***: Works just the same as the sidebar_blacklist function, however controls the display of page titles.

***loop()***: This is a handy function for creating new secondary loops in WordPress with WP_Query. Loop() accepts a post_type (string) as the first parameter, and arguments (array) as the second. See [WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query) for accepted optional arguments. Example usage:

```php
$args = array();
foreach( $view->loop( 'the_post_type', $args ) as $post ) :

	the_title();

endforeach;

Notice that we using $post in our foreach, this is important if you want to be able to use functions like [the_title](http://codex.wordpress.org/Function_Reference/the_title), [the_content](http://codex.wordpress.org/Function_Reference/the_content), [the_permalink](http://codex.wordpress.org/Function_Reference/the_permalink), etc.

Loop() utilizes an [Iterator class](http://www.php.net/manual/en/class.iterator.php) located in lib/FabricLoopIterator.php. The Iterator class automatically performs a number of operations such as: [setup_postdata](https://codex.wordpress.org/Function_Reference/setup_postdata), [wp_reset_postdata](http://codex.wordpress.org/Function_Reference/wp_reset_postdata), and [paginate_links](http://codex.wordpress.org/Function_Reference/paginate_links) (if using pagination).

***paged_loop()***: This function simply points to Loop() while setting a third parameter to enable pagination.

***google_analytics_tracking()***: Google Analytics asynchronous tracking code snippet. If the $google_analytics_id variable at the top of the Base Controller is set, this code will be output in [wp_head](http://codex.wordpress.org/Plugin_API/Action_Reference/wp_head). The action hook associated with this function can be found in the Init Controller __construct.

***the_title()***: Used in views/wrapper.php to output the appropriate title for the currently displayed page. the_title() is only output if the current page is not in the sidebar_blacklist() and $show_title is set to true;

***show_sidebar()***: Responds true/false if the current page supports sidebars.

***show_title()***: Responds true/false if the current page should show the_title().

##### Init Controller

Most of the Controllers used by Fabric are loaded on the [wp hook](http://codex.wordpress.org/Plugin_API/Action_Reference/wp), which is neccessary so that we can load the proper controller type depending on the requested page. The Init controller was designed differently, it fires very early when WordPress is loading, before the [init hook](http://codex.wordpress.org/Plugin_API/Action_Reference/init) fires. Use the Init controller for anything that needs to utilize an [action hook](http://codex.wordpress.org/Plugin_API/Action_Reference) that fires before WP. In the example below we use the __construct function in Init to attach our action hooks for functions from the Base, or any other Fabric controller.

```php
public function __construct()
{
	add_action( 'wp_head', array( $this, 'google_analytics_tracking' ), 99 );
	add_action( 'init', array( $this, 'config' ), 0 );
	add_action( 'init', array( new MyFabricClass, 'my_function' ) );
}

##### Home Controller

The Home controller is used if either of the following conditionals is true: [is_home](http://codex.wordpress.org/Function_Reference/is_home) or [is_front_page](http://codex.wordpress.org/Function_Reference/is_front_page).

##### Other Controllers

All other controllers bundled with Fabric are loaded just like the WordPress [Template Hierarchy](http://codex.wordpress.org/Template_Hierarchy). This means that Fabric will automatically choose the best controller possible based on the page being requested. See below for examples of the order in which controllers are searched for.

**Page**

1. Page{Slug}.php - If the page slug is recent-news, Fabric will look for a controller called PageRecentNews.php
2. Page{id}.php - If the page ID is 6, Fabric will for a controller called Page6.php
3. Page.php
4. Base.php

**Single**

1. Single{Posttype}.php - If the post type were product, Fabric would look for SingleProduct.php.
2. Single.php
3. Base.php 

**Category**

1. Category{Slug}.php - If the category's slug were news, Fabric would look for CategoryNews.php
2. Category{id}.php - If the category's ID were 6, Fabric would look for Category6.php
3. Category.php
4. Archive.php
5. Base.php

This same pattern repeats for Tags, Taxonomies, Custom Post Type Archives, Authors, Date, Search, etc. If you want to know more about how controllers are selected, check out lib/FabricController.php

Notice that Fabric controllers use no underscores, no hyphens, and each word starts with a captial letter (TitleCase). Only one class is used per file, and the name of the class matched the file name. So the class for a file named ArchiveProduct.php would be ArchiveProduct.

### WP Scaffolding

Fabric comes packaged with a supplemental plugin called WP Scaffolding. WP Scaffolding can be used to generate views and controllers for custom post types and taxonomies. WP Scaffolding will actually create the PHP files needed in your theme directory.

This plugin is included in Zip format, and depending on the options you chose during theme activation, it may already be installed and activated. If it is already installed it can be reached via Tools->WP Scaffolding. If it is not installed, it can be found in lib/packages/local_plugins/wp-scaffolding.zip.

See below for some examples of the files that will be generated by WP Scaffolding:

**Creating a custom post type called: Product**

1. ***lib/post-types/product.php***: This file contains the [register_post_type](http://codex.wordpress.org/Function_Reference/register_post_type) function and arguments used to register the new post type.
2. ***controllers/SingleProduct.php***: A new controller for single product pages, extends the Single controller.
3. ***controllers/ArchiveProduct.php***: If the new post type has an archive, a new controller for the archive page will be created, extending the Archive controller.
4. ***views/single-product.php***
5. ***views/archive-product.php***: (If the post type has an archive)

**Creating a custom taxonomy called: Genre**

1. ***lib/taxonomies/genre.php***: This file contains the [register_taxonomy](http://codex.wordpress.org/Function_Reference/register_taxonomy) function and arguments used to regsiter the new taxonomy.

Once a new post type or taxonomy is created, the permalinks are automatically flushed so that your new post types start working immediately. Edits to any generated files can now be done directly the file in your editor of choice.

### Auto Enqueue CSS and JS

Fabric is designed to auto-enqueue certain types of CSS and JS files. These assets can be setup to auto-enqueue in either the footer or the header, and their load order can be configured using optional dependancies.

##### How does it work?

Fabric will *only* act upon files that conform to the specified naming convention, and are placed in either the root of the CSS (assets/css/) or JS (assets/js/) directories.

1. If your filename begins with the text `footer_`, it will automatically be registered and enqueued to appear in the wp_footer.

2. If your filename begins with the text `header_`, it will automatically be registered and enqueued to appear in wp_head.

Example: footer_plugins.js will automatically enqueue itself and load in the footer. If you need to ensure another asset loads first, you can specify the dependancy using a plus(+) and then the handle for the other registered asset. For example if your plugins.js file requires jquery, and you want it to load in the footer, you would name it: footer_plugins+jquery.js.

All auto-enqueued scripts are given a handle based on the filename. footer_plugins+jquery.js would be given the handle "plugins". header_styles.css would be given the handle "styles". The handle is always the name of the file without any dependancies or footer/header prefix. You can use these handles to specify an asset when dealing with dependancies.

### Friendly Cache Busting

When enqueueing assets in WordPress, you can apply a version number to your files. it is common practice to use these version numbers to make sure that visitors to your site do not get an out of data version of the file because it has been cached by their browser. However this method of cache busting has two weaknesses:

1. It relies on the developer to manually increment the version number after making changes
2. Most proxies will not cache resources with a ? in the URL

To address the first issue: for any auto-enqueued CSS and JS files, Fabric will automatically increment the version number every time the file is modified. This is done by using the [filemtime](http://us3.php.net/filemtime) PHP function to get the timestamp of the last modification time of the file, and using that timestamp as the version.

As for the second issue: Fabric uses a couple simple rewrites to move the version name inside of the file. So `footer_plugins.js?ver=1391108295` becomes `footer_plugins.fabric_1391108295.js`. A rewrite rule is added with the WordPress [generate_rewrite_rules](http://codex.wordpress.org/Plugin_API/Action_Reference/generate_rewrite_rules) hook to make sure that the correct file is still found when requested.

If you are enqueing your own assets outside of the auto enqueue system, and you wish to add your own versions to registered scripts, please make sure that your version complies with the allowed charactors: [a-zA-Z0-9_].

### Clean Up

Lorem Ipsum

### Template Wrapper

Lorem Ipsum

### SASS Compiling with Grunt

#### Grunt how-to
1. Clone or download repo into appropriate project folder
2. `cd` to that directory in your terminal
3. run `npm install` from the command line (installs grunt and assorted dependencies defined in `package.json` file)
4. run `grunt dev` from the command line to:  
  a. start a server at port :8000 for live-reloading  
  b. run `grunt watch` which will do the following at any file save:  
    1. prefix any necessary CSS3 declarations you were too lazy to handle yourself  
    2. process, concatenate, and minify .scss & .js files with proper naming and locations  
    3. compress any images in the `img` directory (excluding svgs; see note in Gruntfile if you need svg support)
    4. let LiveReload know what's up (I use [the browser extension](http://feedback.livereload.com/knowledgebase/articles/86242-how-do-i-install-and-use-the-browser-extensions-))  

##### Dependencies:

1. [Node.js/npm](http://nodejs.org/)  
2. [Grunt](http://gruntjs.com/)  
3. [Compass](http://compass-style.org/)  
4. [Bourbon](http://bourbon.io): **installed via Grunt**. If you want to use it but *not* use Grunt, run `bourbon install` in your terminal from within the assets directory
5. [Neat](http://neat.bourbon.io/): **installed via Grunt**. If you want to use it but *not* use Grunt, run `install neat` in your terminal from within the assets directory
6. [Sass Globbing](https://github.com/chriseppstein/sass-globbing) (allows for *much* neater Sass imports)  
7. [LiveReload](http://livereload.com/) (Optional, but only if you hate yourself)  

##### Notes
1. You'll need to change the Gruntfile `concat`, `uglify`, & `watch` objects if you're writing coffeescript. Basically just change the `*.js` values to `*.coffee`  
2. If you don't want the Compass support you'll need to edit the Gruntfile before you get going. I think that's just plain crazypants, but whatever, man, free country and all that. Here you go:  
  a. on line 14, remove the `compass: true` item (or change it to false, which is the default)  
  b. remove the `@import "compass"` line from the `style.scss` (line 8)  