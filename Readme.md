
[![Version](https://poser.pugx.org/blocksystems/file-attributes/version?format=flat)](https://packagist.org/blocksystems/file-attributes)
[![License](https://poser.pugx.org/blocksystems/file-attributes/license?format=flat)](https://packagist.org/packages/blocksystems/file-attributes)

## Document
<p>Set auto file attributes</p>

[in your model class]
```php
protect $fileable = ['thumbnail', 'profile'];
```

`e.g` (when model creating)
```php
User::create(['thumbnail' => $request->file('thumbnail'), 'profile' => $request->file('profile')]);
$user->thumbnail; // will be 's3 or local path'
```
      
`e.g` (multiple files ※but if you want that, please cast your attribute to json like "`php protected $casts = ['attirbute' => 'json']`") 
```php
User::create(['thumbnail' => [$request->file('thumbnail'), $request->file('profile')]]);
```
##Set prefix file path
```php
protect $filablePrefix = 'admin'; // including slash or not, everything is OK.
```
##Set automatically deleting file when attributes changed or deleted
```php
protect $fileAutoDeleting = true;  // default : true
```
##Everytime when attribute get changed value, will change dynamically file to url string
```php
protect $fileableSync = true; // default : false
```
`e.g`
```php
$user->thumbnail = $request->file('thumbnail');
// (without saving) will be 's3 or local path'
```