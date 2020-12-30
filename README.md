## Laravel Celtic LTI

This is a package to integrate [Celtic LTI](https://github.com/celtic-project/LTI-PHP/) with Laravel.

## Installation

Require this package with composer.

```shell
composer require longhornopen/laravel-celtic-lti
```

Run your app's migrations, to install the 'lti2_*' database tables.

```php
php artisan migrate
```

### Laravel without auto-discovery:

If you don't use Laravel's auto-discovery of packages, add the ServiceProvider to the providers array in config/app.php:

```php
LonghornOpen\LaravelCelticLti\LtiServiceProvider
```

## Usage

Here's a sample set of steps to get this library up and running in your app.

* If your app uses the VerifyCsrfToken middleware (which it does by default), add 'lti' to the $except array in that middleware.
* Add a route to routes/web.php, handling LTI traffic: 
```php
Route::post('/lti', [App\Http\Controllers\LtiController::class, 'ltiMessage']);
```
* Create a new Controller to respond to this traffic: 
```php
use LonghornOpen\LaravelCelticLTI\LtiTool;
use Illuminate\Http\Request;

class LtiController extends Controller
{
    public function ltiMessage(Request $request) {
        $tool = new LtiTool();
        $tool->handleRequest();

        // $tool contains information about the launch - which LMS, course, placement, and user this corresponds to.
        // Store these in your database or session, as appropriate for your app.
        if ($tool->getLaunchType() === $tool::LAUNCH_TYPE_LAUNCH) {
            $consumer_guid = $tool->platform->consumerGuid; // lms
            $lti_context_id = $tool->context->ltiContextId; // course
            $lti_resource_link_id = $tool->resourceLink->ltiResourceLinkId; // placement
            $lti_user_id = $tool->userResult->ltiUserId; // user
            $course_name = $tool->context->title;
            $user_name = $tool->userResult->fullname;
            $user_email = $tool->userResult->email;
            ...
```
* Create a Platform database entry for each installation of your app, using either:
  * the artisan commands (not written yet)
  * or raw PHP code, as described in (the Celtic docs)[https://github.com/celtic-project/LTI-PHP/wiki/Usage#initialising-a-platform]
* Install your tool in your LMS.  All LTI URLs should be the '/lti' route you created above.

## Contributing

We gladly accept Github issues containing bug reports or suggestions for improvement.

Pull requests or other offers of development help are appreciated.  If you're wanting to contribute a large change, please open an issue and let us know.


## TODO

* Identify other useful utility code and add it here
* Write an artisan command to generate LTI consumers
* Publish this publically on Packagist
