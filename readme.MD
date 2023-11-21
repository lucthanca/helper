### MageTwo Dump Helper
#### 1. Installation
* cd to app folder
* clone this repo
* for php version <= 7.1, you should checkout to branch `php71`
* run `composer install`
* chmod 775 var/log folder
* go back to magento root folder, open `composer.json`, add this line after `app/etc/NonComposerComponentRegistration.php`
```json
"app/<cloned_git_folder>/Helper.php"
```
* run `composer dump-autoload`