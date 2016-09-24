const elixir = require('laravel-elixir');

process.env.DISABLE_NOTIFIER = true;
elixir.config.assetsPath = '../concrete';
elixir.config.css.less.folder = 'css';

// Javascript
require('./gulp-tasks/scripts.js');

// Less and css
require('./gulp-tasks/stylesheets.js');
