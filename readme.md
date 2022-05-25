Hashed Passport
-----
Hashed Passport turns Passport's public Client IDs from this:

`client_id: 1` 

into the more pleasing, beautiful and industry standard:

`client_id: AE20JvpmdYx34wD789Lng5jyqelQar8R`

While not touching any of the core Passport files. It uses a middleware decode the `client_id` on routes and an observer to make the hashed id available through the `client_id` parameter of the `\Laravel\Passport\Client` anywhere in the application.

Encryption of the client secrets is optional. These are saved in plain-text by default. After enabling this feature Hashed Passport turns a database entry of `wOVl4sBrTU46KwaiV56yc9IftikEIcKfWYCpwosG` 

into
 
`eyJpdiI6IkhDQlYyZDBpeUVCVHRsZGFcL3ZiejRBPT0iLCJ2YWx1ZSI6InFoUGRKcUFRaVwvc2t3Q1ZhVHhqM3lpaW05cm1FaXpObUtyNmd4QXNMU21mVmNhNW45N0lVTHJLa2prYlJpcmpnQzJqMTRXS1c3NWlaR2tcL01ZZmFNXC9RPT0iLCJtYWMiOiJhYzJmOTMwZWE2NTI4MWZiMTAxNDg5NTQ2NmFiNDU2YmZmOTcxOTIzMTVmNTU2Njk1N2ZlNzg5MzFiNmI5MTUzIn0=` 

Introduction
-----

The idea for this package originated when I first started working with Laravel Passport. Having made a few APIs using Lumen and JWT it was jarring to discover that Passport used the auto-increment integer as the Client ID.

The pros and cons are all listed in this 2 year old issue. You can tell just _how_ old it is by looking at the ID of the issue ;-)

[Original Laravel/Passport Github Thread](https://github.com/laravel/passport/issues/14)

Changelog
---
* 1.0 - Non-intrusive implementation of hashed Passport client IDs.
* 1.1 - Non-intrusive implementation of encrypted Passport client secrets.
* 1.2 - Added the option to customise the hashing salt, cleaned up the code and improved the readme.
* 2.0 - Works with Laravel Passport 7.0 and includes tests. Major thanks to Hackel and other contributors.

Requirements
-----

**OPTIONAL: Encrypted Secrets**
* A database supporting `VARCHAR(2048)`. If you use MySQL this means version 5.0.3+


Installation
-----

##### Hashed Client IDs

* Install via composer `composer require ssmulders/hashed-passport`

That's it! If you haven't tinkered with any default Laravel Passport routes, you're all set. 

##### OPTIONAL: Encrypted Secrets
* Add `HashedPassport::withEncryption();` to the register method of your `AppServiceProvider`.

* Run the `php artisan migrate`

* Run `php artisan hashed_passport:install`

Voila! Your secrets are now stored with encryption and automatically decrypted after a DB retrieval.

##### OPTIONAL: Overwrite the APP_KEY Hashing Salt
* Run `php artisan vendor:publish --provider="Ssmulders\HashedPassport\HashedPassportServiceProvider"` 
 
Now update the `salt` key in `config/hashed-passport.php` with a custom string.

Further Usage and Customisation
-----

Anywhere you access the `Laravel\Passport\Client` model a `client_id` parameter is made available. This is the hashed client id. 

#### Add Middleware to additional Routes
After installation `/oauth/token` route will now accept both the the hashed id and the regular integer id on incoming requests.

To add this functionality to any other route, just attach the `hashed_passport` middleware like so:

`Route::get('/oauth/clients', '\Laravel\Passport\Http\Controllers\ClientController@update')->middleware(['hashed_passport']);`

The incoming hashed `client_id` string will now automatically be converted to it's integer value before being processed further by the application.

#### Change the hash salt
By default the hashing salt is the `APP_KEY`. To override this behaviour publish the config file to set a custom salt:

`php artisan publish:config`

How it works
-----

#### Hashed ID
In order to work out-of-the-box the package overwrites the oAuth2 token route to accept the Client ID as a hashed string. Further more the hashed client id is made available on all Client models through the `client_id` parameter thanks to the `ClientObserver`. This same `ClientObserver` takes care of the encrypting and decrypting of the client secret (when enabled). 

The hashed id is still based on the `oauth_clients` table's `index` column. All it does is transform that ugly integer into a beautiful hashed string, Cinderella style.

To do this a new connection is added to Hashids called `client_id`. This salt is based on your `APP_KEY` (unless configured otherwise).

Supported grant types:

`password`: **verified**

`client_credentials`: **verified**

`authorization_code`: **untested (probably needs the middleware added to other routes)**

##### Encrypted Secret
Similar to the hashed ID, the observer catches the `Client` when it's being saved and retrieved from the database. 

Since the default `VARCHAR(100)` value for the `secret` column is insufficient for storing the lengthier encrypted secrets, that needs to be updated to `VARCHAR(2048)`. It could be less, but I'd rather be safe than sorry. The actual maximum column character length has no impact on storage usage.

Running `php artisan hashed_passport:install` will then encrypt all the existing rows so their secret is encrypted. Once successful, you'll be greeted with a large lock. From now on all your client secrets are stored safely with Laravel's `encrypt()` helper.



Troubleshooting
-----

* The package should be compatible with any installation and can even be used with projects that are using the integer client_id in production, as it will support both versions of the client_id and encrypt the secrets of all clients.

* Check your routes to make sure that `/oauth/token` uses the `hashed_passport` middleware: 
`php artisan route:list`. The same applies for any other routes you want to accept requests with hashed `client_id` from.


Uninstall
-----

Should support for hashed IDs and encrypted secrets be added to Laravel Passport in the future, all you'd need to do in order to revert back to normal is follow these steps:

Run `php artisan hashed_passport:uninstall`  to revert back to plain text secrets in your database.

Run `composer remove ssmulders/hashed-passport` to remove the package.

Remove `HashedPassport::withEncryption();` from your `AppServiceProvider`

Run this SQL command `DELETE FROM 'migrations' WHERE migration LIKE '%hashed_passport%'` to update your migrations table and prevent rollback errors. `php artisan migrate:refresh` will work fine though.

Feedback
---

It's my first ever package so if you have any feedback on whatever, please leave a message!  Learning how to create a package has opened up a whole new bag of appreciation for the wonder that's known as Laravel.

Feel free to open pull request(s) that improve on the functionality, or fix any bugs. 

Credits
---
Thanks to [@hfmikep](https://github.com/hfmikep) and [@nikkuang](https://github.com/nikkuang) for being the first to properly start tackling this long-standing feature request. And once again to [@hfmikep](https://github.com/hfmikep) for for creating the code that's used in the `UnHashClientIdOnRequest` middleware. And to [@corbosman](https://github.com/corbosman) for requesting the client secret encryption.

License
---
Copyright (c) 2018 Stan Smulders

MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

