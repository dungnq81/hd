let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const dir = 'wp-content/plugins/' + directory;

const resources = dir + '/resources';
const assets = dir + '/assets';

mix
	.disableNotifications()

	.js(resources + '/js/admin_custom_order.js', assets + '/js')