# Alt Google 2FA Addon

> Google 2FA - just not requiring like, an hour of setup and config.

Heavily uses https://github.com/antonioribeiro/google2fa-laravel - big shout out 🫶

## Features

- Select who needs 2FA enforced by super users and roles
- Select optional user roles that can access 2FA
- Works on Front End and Control Panel

## How to Install

``` bash
composer require alt-design/alt-google-2fa
```

## Basic usage

### Settings
Just nip into the settings, few options

- Enforced / Optional / Off
  - **Enforced** - Users matching the criteria _have_ to have 2FA on to access the site.
  - **Optional** - As it says on the tin, the routes are available if you fancy.
  - **Off** - Just turns off the 2FA requirements for Super Users

### Using your own template
Want to use your own template? Don't blame ya! Luckily we've built a tag in to generate the QR code. Template just needs to vaguely look like this:
```
<!-- QR Tag -->
<s:AltGoogle2FA /> (Blade) or {{ alt-google-2fa }} if you're using Antlers)

<!-- OTP Form ->
<form action="{{ route('alt-google-2fa.verify') }}" method="POST">
    @csrf

    <input name="one_time_password" type="text" placeholder="OTP Code">

    <button type="submit" class="btn btn-primary">Authenticate</button>
</form>

<!-- Just so people don't get stuck -->
<a href="{{ route('statamic.logout') }}"> 
    Cancel & Logout
</a>
```

### Example manual inclusion of enabling/disabling 2FA.
```
{{ if {AltGoogle2FA:is-enabled} }}
    <a href="{{ route:alt-google-2fa.disable-2fa }}">Disable</a>
{{ else }}
    <a href="{{ route:alt-google-2fa.enable-2fa }}">Enable</a>
{{ /if }}
```

## Using Database Users?

No problemo! You'll just need to publish the database migration and run it!

```
php artisan vendor:publish --tag=alt-google-2fa-migrations
php artisan migrate
```

## Locked yourself out

Oop - you can just disable the settings using the content/alt-google-2fa/settings.yaml, or remove the 2FA field values on your user.

## Questions etc

Drop us a big shout-out if you have any questions, comments, or concerns. We're always looking to improve our addons, so if you have any feature requests, we'd love to hear them.

Also - check out our other Statamic bits!

### Starter Kits
- [Alt Starter Kit](https://statamic.com/starter-kits/alt-design/alt-starter-kit) 

### Addons
- [Alt SEO Addon](https://github.com/alt-design/Alt-SEO-Addon)
- [Alt Redirect Addon](https://github.com/alt-design/Alt-Redirect-Addon)
- [Alt Sitemap Addon](https://github.com/alt-design/Alt-Sitemap-Addon)
- [Alt Akismet Addon](https://github.com/alt-design/Alt-Akismet-Addon)
- [Alt Password Protect Addon](https://github.com/alt-design/Alt-Password-Protect-Addon)
- [Alt Cookies Addon](https://github.com/alt-design/Alt-Cookies-Addon)
- [Alt Inbound Addon](https://github.com/alt-design/Alt-Inbound-Addon)

## Postcardware

Send us a postcard from your hometown if you like this addon. We love getting mail from other cool peeps!

Alt Design  
St Helens House  
Derby  
DE1 3EE  
UK    

