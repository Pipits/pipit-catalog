# Pipit Catalog
The Catalog App provides an alternative listing page to your Shop Products with extra features such as the inclusion of product thumbs and easy filtering options. The listing also highlights items that need your attention such as out of stock items, low stock items, unset price and sale items.

## Installation
- Download the latest version of the Catalog App.
- Unzip the download
- Place the `pipit_catalog` folder in `perch/addons/apps`


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


### Product thumb
Perch generates thumbs for images uploaded via the Assets uploader by default. The app uses these thumbs, but you can configure it to use other versions if they exist.

In the settings you have fields for `width`, `height`, `density` and `crop`. Leave these options empty if you are happy with the default thumbs.


The default product template `product.html` generates thumbs with `width="80" height="80" density="1.6" crop="true"`:

```markup
<perch:shop id="image" type="image" label="Main product image" order="4" width="800" />
<perch:shop id="image" type="image" width="80" height="80" density="1.6" crop="true" />
```

You can use the square 80x80 version by entering the thumbs' details in the settings.

If your thumb doesn't have the `density` attribute, leave the field empty in the settings. If it doesn't have the `crop` attribute, leave it unchecked.


### Other options
You can choose to hide some filters, the search field and the products' thumbs if they are not useful to you.