# honeycomb-scripts  
https://github.com/honey-comb/scripts

## Description

HoneyComb CMS Automated code generation

## Attention

This is part scripts package for HoneyComb CMS package.

If you want to use laravel version 5.5.* [use scripts package version 0.1.*](https://github.com/honey-comb/resources/tree/5.5 "Resources package version 0.1.*")

## Requirement

 - php: `^7.1`
 - laravel: `^5.6`
 - composer
 
 ## Installation

Begin by installing this package through Composer.


```js
	{
	    "require": {
	        "honey-comb/scripts": "2.*"
	    }
	}
```
or
```js
    composer require honey-comb/scripts
```

# Usage

### Creating a honeycomb service

Honeycomb Service in honeycomb terms means Routes, Models, Controllers, Services, Repositories, Forms, Requests as one which can be access via url in admin panel.

- create **_hc_scripts_configuration folder** in project root directory
- create config.json file i.e. **w_rewards.json**
- run `php artisan hc-make:service` it will generate files from given configuration (looks for .json files). It generates 1 file per run and add `.done` extension after success.

**w_rewads.json example:**
```
{
  "directory": "",
  "url": "rewards",
  "icon": "scroll",
  "serviceName": "WReward",
  "multiLanguage": 1,
  "forms": ["new", "edit"],
  "optionLabelList" : ["label"],
  "models": [
    {
      "tableName": "w_reward",
      "modelName": "WReward",
      "default": 1,
      "repository": 1,
      "use": ["translations"]
    }
  ],
  "actions": {
    "admin": [
      "list",
      "create",
      "update",
      "delete",
      "delete_force",
      "restore"
    ],
    "front": [
    ],
    "api": [
    ]
  }
}
```
- directory - if you are developing package your can write **honey-comb/core** and all files will be generated in this dir. Using for project leave empty `""`
- url - admin access url i.e. `project.local/admin/rewards`
- icon - icon for admin meniu element
- serviceName - used to generate all file prefixes. (must be the same as main `modelName` read below)
- multiLanguage - if service has multilanguage fields than write **1** otherwise **0**. If multilanguage is *1* than `w_reward` and `w_reward_translation` table is required  otherwise  only `w_reward` table.
- forms - dont change
- optionLabelList - dont change
- models - list of models which will be generated. There always must be a model to generate a service. (database table must exist)
   - default - 1 means it will be used for controller and resositry to auto generate default model
   - modelName better must be the same as `serviceName`
   - respository - dont change
   - use - if `multiLanguage` is set to 1 then write `translations` option otherwise leave empty array `"use":[]`
   you can add multiple models if you want. But they cannot be `default:1`.

- actions - is related to admin `ACL`. Here listed all needed actions. You can add additional actions if you need.

 
