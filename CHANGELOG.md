# 4.0.0
#### Shopware compatibility update
- This version is compatible with Shopware 6.6.0 and higher.

#### New features
- Added extra headers to the API calls. This enables us to provide better support by monitoring used Shopware and plugin versions.

# 3.1.2
- An incompatibility with the returns management section of the Shopware Commercial plugin was discovered, which caused the PostNL order overview to no longer work. A workaround has been implemented
- (Minor) Fixed missing images in the plugin configuration

# 3.1.1
- Fixed problems editing orders in the administration

# 3.1.0
- Added new Belgium to Netherlands products for shipping and pickup points
- Added an ID/age check option for shipping and pickup points in the Netherlands
- Fixed an issue whereby the pickup point location code was not stored on the order. Affected all 3.0 versions.
- Fixes an issue when changing the shipping method in the administration would always select NL as the sender's country
- Added support for MariaDB versions older than 10.5.2
  - If you've already tried installing the plugin on an older MariaDB version, then take these steps to remove the plugin data before trying to install the new version:
    - Remove any database table starting with `postnl_` from the database
    - Remove all entries from the `migration` database table where the `class` field starts with `PostNL\Shopware6`
    - Optional: Remove the old plugin files first and refresh the plugin listing in the admin

# 3.0.2
- Fix an issue whereby selected delivery dates or pickup points were not stored.
- Added delivery date and send date to the admin order detail page again.

# 3.0.1
- Multiple small bugfixes

# 3.0.0
#### Shopware compatibility update
- This version is compatible with Shopware 6.5.2 and higher.

#### Fixes
- Fixed an issue where no default product was selected during checkout.
- Fixed an issue whereby shipping date and chosen delivery date were not displayed in the administration.

#### Known issues
- In Shopware versions lower than 6.5.5.0 the PostNL icons are not available in the administration. This is a cosmetic issue only, and does not affect functionality. 

# 2.0.0
#### New features
- Added delivery date selection in the checkout, including evening delivery.
  - Configure these in the plugin's settings.
- New European and international shipping options have been added.
  - GlobalPack has been replaced.

# 1.2.3
- Fixes non-required fields becoming required when selecting a country other than the Netherlands during registration.
- Fixes certain cards not displaying on the order detail page in the administration when opening a non-PostNL order.

# 1.2.2
- Fixes an issue with addresses when shipping address is different from billing address.
- Fixes an issue where emails could not be sent if there was no order data (e.g. Password recovery)

# 1.2.1
- Fixes an issue with custom fields on the selected shipping address (Thank you Mitchel van Vliet and Robbert de Smit @ DutchDrops)

# 1.2.0
- Addresses of pickup points are now stored as the shipping address when selecting a pickup point
  - The original selected address is still available on the order entity

# 1.1.0
#### Belgian release
- Product codes for shipping from Belgium have been added.

#### Bugfixes
- Fixes an issue where, after entering an invalid Dutch address and then switching to another country, the customer registration could not be completed.

# 1.0.0
#### Initial release
- Easily register shipments with PostNL.
- Easily print the shipping labels.
- Use one of the many PostNL shipping methods (e.g. letterbox package, insured shipping).
- Send your parcels easily to Belgium, Europe and the rest of the world.
- Address validation for Dutch addresses.
- Let your customers choose whether they want to receive the parcel at home or pick it up from a PostNL point near them.
- Easily share the return label with your customers.
- Choose which format shipping label to print (A4 or A6).
- Activate alternative shipping method above a certain order amount.
