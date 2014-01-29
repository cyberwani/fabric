# Fabric

Fabric is a lightweight WordPress stack for creating better custom themes in less time.

## Attribution
Built by [UpTrending](http://uptrending.com)

#### Grunt how-to
1. Clone or download repo into appropriate project folder
2. `cd` to that directory in your terminal
3. run `npm install` from the command line (installs grunt and assorted dependencies defined in `package.json` file)
4. run `grunt dev` from the command line to:  
  a. start a server at port :8000 for live-reloading  
  b. run `grunt watch` which will do the following at any file save:  
    1. prefix any necessary CSS3 declarations you were too lazy to handle yourself  
    2. process, concatenate, and minify .scss & .js files with proper naming and locations  
    3. compress any images in the `img` directory (excepting svgs; see note in Gruntfile if you need svg support)  
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