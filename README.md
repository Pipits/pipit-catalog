# Pipit Catalog

The Catalog App provides an alternative listing page to your Shop Products with extra features such as the inclusion of product thumbs and easy filtering options. The listing also highlights items that need your attention such as out of stock items, low stock items, unset price and sale items.

## Installation

- Download the latest version of the Catalog App.
- Unzip the download
- Place the `pipit_catalog` folder in `perch/addons/apps`
- Add `pipit_catalog` to your runtime apps list in `perch/config/apps.php`

### Requirements

- Perch or Perch Runway 3.0 or higher

## Configuration

Options related to the Catalog app will apear in the Settings Page in Perch.

### Product Category Set

Select the primary category Set you are using for organising your Perch Shop store. This is required to enable filtering by Category in the Catalog App.

### Highlight low stock

When left empty, the low-stock warning highlight appears when a product's stock status is set to `Low Stock`.

Set it to the stock level (integer) at which you want low-stock warning highlight to appear (for all products). For example, set it to `20` and any product who has 20 or fewer items left in stock will be highlighted.

This only works when the stock count is central for the product. This option is ignored if a product's stock count is `on individual variants`.

### Display sale prices

When an item is on sale, you can choose to display the sale price instead of the regular price. When this option is enabled, sale prices are highlighted.

### Other options

You can choose to hide some filters, the search field and the products' thumbs if they are not useful to you.

## Reordering products

You can reorder the products in 3 contexts:

1. Globally
2. Within a category (beta)
3. Within a brand (beta)

### Displaying products in global order

To display the products on your website in the same order you define through the Catalog app, you need to set your sort options:

```php
perch_shop_products([
    'sort' => 'productOrder',
    'sort-type' => 'numeric',
    'sort-order' => 'ASC'
]);
```

### Displaying products in category order

```php
pipit_catalog_products('category', 'products/shoes/', [
    'template' => 'products/list.html'
]);
```

You can exclude products from sub-categories:

```php
pipit_catalog_products('category', 'products/shoes/', [
    'template' => 'products/list.html',
    'sub-categories' => false,
]);
```

### Displaying products in brand order

```php
pipit_catalog_products('brand', 'brand-slug', [
    'template' => 'products/list.html'
]);
```

## Functions

### pipit_catalog_products()

The function displays the products as ordered via the control panel within a brand or a category.

```php
pipit_catalog_products($for='', $id='', $opts=[], $return=false);
```

| Type    | Description                                                 |
| ------- | ----------------------------------------------------------- |
| String  | `brand` or `category`                                       |
| String  | Brand's slug or Category's path                             |
| Array   | Options array                                               |
| Boolean | Set to `true` to have the value returned instead of echoed. |

### pipit_catalog_get_variants()

The function returns an array containing the product's key variants data, which can be used in JS for the add-to-cart form (e.g. update stock and price per variant).

```php
pipit_catalog_get_variants($slug);
```

Typically this will be used with Runway's headless mode:

```php
$Headless       = $API->get('HeadlessAPI');

$Response       = $Headless->new_response();
$ProductsSet    = $Headless->new_set('products');
$OptionsSet     = $Headless->new_set('options');


$out = pipit_catalog_get_variants(perch_post('s'));
$ProductsSet->add_items($out['products']);
$OptionsSet->add_items($out['options']);


$Response->add_set($ProductsSet);
$Response->add_set($OptionsSet);
$Response->respond();
```

```js
let data = {};
let xhr = new XMLHttpRequest();
let formData = new FormData();

formData.append("s", "product-slug");
xhr.open("POST", "/api/products/variants", true);
xhr.send(formData);

xhr.onload = function () {
  if (xhr.status == 200) {
    data = JSON.parse(this.responseText).sets;
    console.log(data);
  }
};
```
