# 1.2.3
- Fixes non-required fields becoming required when selecting a country other than Netherlands during registration.
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
