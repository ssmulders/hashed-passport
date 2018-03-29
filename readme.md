Hashed Passport
-----
Hashed Passport turns Passport's public Client IDs from this:

`client_id: 1` 

into the more pleasing, beautiful and industry standard:

`client_id: AE20JvpmdYx34wD789Lng5jyqelQar8R`

While not touching any of the core Passport files. It basically uses 2 middlewares to encode and decode the `client_id` on routes.


Introduction
-----

The idea for this package originated when I first started working with Laravel Passport. Having made a few APIs using Lumen and JWT it was jarring to discover that Passport used the auto-increment integer as the Client ID.

The pros and cons are all listed in this 2 year old issue. You can tell just _how_ old it is by looking at the ID of the issue ;-)

[Original Laravel/Passport Github Thread](https://github.com/laravel/passport/issues/14)


Installation
-----

* Install hashed-passport via composer for your project:

`composer require ssmulders/hashed-passport`

No additional steps are needed.

Usage
-----

All clients returned by the `/oauth/clients` route now contain the hashed id accessible by the `client_id` parameter.

When sending a request to a route with the `hashed_passport` middleware, the `client_id` key will automatically be decoded to it's integer value for further processing by the application.

You can easily add the middleware to other Passport routes by overwriting them by adding them to your `web.php` or `api.php` routes file and add the `hashed_passport` middleware like so:

`Route::get('/oauth/clients', '\Laravel\Passport\Http\Controllers\ClientController@update')->middleware(['web', 'auth', 'hashed_passport']);`

How it works
-----

In order to work out-of-the-box the package overwrites the oAuth2 token and client routes to serve and accept the Client ID as a hashed string. It's still based on the `oauth_clients` table's `index` column. All it does is transform that ugly integer into a beautiful hashed string, Cinderella style.

It adds a new connection to Hashids called `client_id`. This salt is based on your `APP_KEY`. It should be unique to all projects, while constant enough to be usable as a salt.

Supported grant types:

`password`: **verified**

`client_credentials`: **verified**

`authorization_code`: **untested (probably needs the middleware added to other routes)**

Troubleshooting
-----

* The package should be compatible with any installation and can even be used with projects that are using the integer client_id in production, as it will support both versions of the client_id.

* Should support for hashed IDs be added in the future, all you'd need to do was update your package.

* Check your routes to make sure the token and client ones use the `hashed_passport` middleware: 
`php artisan route:list`


Feedback
---
I have only tested this with the password grant however I see no reason why it won't work with all the others. Feel free to open pull request(s) that include full grant compatibility. 

It's my first ever package so if you have any feedback on whatever, feel free to leave a message! 

Learning how to create a package has opened up a whole new bag of appreciation for the wonder that's known as Laravel!


Credits
---
Thanks to [@hfmikep](https://github.com/hfmikep) and [@nikkuang](https://github.com/nikkuang) for being the first to properly start tackling this long-standing feature request. And once again to [@hfmikep](https://github.com/hfmikep) for for creating the code that's used in the `UnHashClientIdOnRequest` middleware.

License
---
Copyright (c) 2018 Stan Smulders

MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

