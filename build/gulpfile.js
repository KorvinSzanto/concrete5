const elixir = require('laravel-elixir');

elixir.config.assetsPath = '../concrete';
elixir.config.css.less.folder = 'css';

// Javascript
require('./gulp-tasks/scripts.js');

// Less and css
require('./gulp-tasks/stylesheets.js');
