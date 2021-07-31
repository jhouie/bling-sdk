*Work in progress*

## Introduction

This SDK was created to make it easy to access and use all the features made available in the Bling API.
You can see all available resources and the full API documentation at the link below:

[Bling API Documentation](https://ajuda.bling.com.br/hc/pt-br/categories/360002186394-API-para-Desenvolvedores)

## Index

- [Installation](#installation)
- [Usage](#usage)
- [Repositories](#repositories)
  - [Categories](#categories)
    - [List all categories](#list-all-categories)
    - [Get a single category](#get-a-single-category)
    - [Create a category](#create-a-category)
    - [Update a category](#update-a-category)

## Installation

You can easily install this package through composer using the command below:

```shell
composer require jhouie/bling-sdk
```

## Usage

In order to use the SDK, you only need to instantiate the class passing your API key to the constructor

```php
<?php

require 'vendor/autoload.php';

$bling = new \Bling\Bling('your_api_key_goes_here');
```

Each endpoint is represented by a repository class, after creating a Bling object you can access all the available repositories using the class methods like this:

```php
$categoryRepository = $bling->categories();
```

## Repositories

### Categories

```php
<?php

require 'vendor/autoload.php';

$bling = new \Bling\Bling('your_api_key_goes_here');

$categoryRepository = $bling->categories();

```

#### List all Categories:

```php
$categoryRepository->all();
```

#### Get a Single Category

```php
$categoryRepository->find('category_id');
```

#### Create a Category

```php
$categoryRepository->create([
    'descricao' => 'Category description',
    'idCategoriaPai' => '12345'
]);
```

#### Update a Category

```php
$categoryRepository->update([
    'descricao' => 'Updated category description'
], 'category_id');
```
