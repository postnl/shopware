<?php declare(strict_types=1);

namespace PostNL\Shopware6;

class Defaults
{
    const CUSTOM_FIELDS_KEY = 'postnl';

    const LINEITEM_PAYLOAD_WEIGHT_KEY = 'weight';
    const LINEITEM_PAYLOAD_TARIFF_KEY = 'hsTariffCode';
    const LINEITEM_PAYLOAD_ORIGIN_KEY = 'countryOfOrigin';

    const PRODUCT_MAILBOX_NL_NL = self::PRODUCT_MAILBOX_NL_NL_2928;
    const PRODUCT_SHIPPING_NL_NL = self::PRODUCT_SHIPPING_NL_NL_3085;
    const PRODUCT_PICKUP_NL_NL = self::PRODUCT_PICKUP_NL_NL_3533;

    const PRODUCT_SHIPPING_NL_BE = self::PRODUCT_SHIPPING_NL_BE_4946;
    const PRODUCT_PICKUP_NL_BE = self::PRODUCT_PICKUP_NL_BE_4936;

    const PRODUCT_SHIPPING_BE_BE = null;
    const PRODUCT_PICKUP_BE_BE = null;

    // NL->NL, Verzending
    const PRODUCT_SHIPPING_NL_NL_3085 = "01c8aeac08cd4d1b95de9ef6a18ae89d";

    // NL->NL, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPPING_NL_NL_3385 = "6aa1d2225d724416bea415e2454de832";

    // NL->NL, Verzending, retour b.g.g.
    const PRODUCT_SHIPPING_NL_NL_3090 = "ed587b9fa00b421f94e98a0c85df4124";

    // NL->NL, Verzending, niet bij buren bezorgen, retour b.g.g.
    const PRODUCT_SHIPPING_NL_NL_3390 = "79120b803f6d46e29c300ab46d695c6e";

    // NL->NL, Verzending, verzekerd
    const PRODUCT_SHIPPING_NL_NL_3087 = "70c07296123e463cbb19cedfad9c6f02";

    // NL->NL, Verzending, verzekerd, retour b.g.g.
    const PRODUCT_SHIPPING_NL_NL_3094 = "ad98fc8e8165466eb5485edcfdaa6b6c";

    // NL->NL, Verzending, handtekening, niet bij buren bezorgen
    const PRODUCT_SHIPPING_NL_NL_3089 = "6b4a2b10b13e4b44a7a4db94d8f4736e";

    // NL->NL, Verzending, handtekening, niet bij buren bezorgen, retour b.g.g.
    const PRODUCT_SHIPPING_NL_NL_3096 = "75583df93af54e368449cac960089142";

    // NL->NL, Verzending, handtekening
    const PRODUCT_SHIPPING_NL_NL_3189 = "ebee6dfe35394d429c3126842f391183";

    // NL->NL, Verzending, handtekening, retour b.g.g.
    const PRODUCT_SHIPPING_NL_NL_3389 = "8cca6f28f4fd421a8128645266dc780a";

    // NL->NL, Pickup, handtekening
    const PRODUCT_PICKUP_NL_NL_3533 = "99280b9c174947a5894ac5cdd26c7ae8";

    // NL->NL, Pickup, verzekerd
    const PRODUCT_PICKUP_NL_NL_3534 = "820a1909fbb840ee94ad56cfed05bc8f";

    // NL->NL, Pickup, handtekening, notificatie
    const PRODUCT_PICKUP_NL_NL_3543 = "28cad6470c0f44ea8b3586cc3c6cab2e";

    // NL->NL, Pickup, verzekerd, notificatie
    const PRODUCT_PICKUP_NL_NL_3544 = "1238b1f6b0744dcdbe61d6350207059d";

    // NL->NL, Brievenbuspakje
    const PRODUCT_MAILBOX_NL_NL_2928 = "62e456542fa843d3b3140622ea9b3547";

    // NL->BE, Pickup
    const PRODUCT_PICKUP_NL_BE_4936 = "5a1d77cf89ec416f8c92f440ba961c6b";

    // NL->BE, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPPING_NL_BE_4941 = "00dfac1c0dbd4b7480dc519e864e04b0";

    // NL->BE, Verzending
    const PRODUCT_SHIPPING_NL_BE_4946 = "7f68f71067214ad4899862d87ac85950";

    // NL->BE, Verzending, handtekening
    const PRODUCT_SHIPPING_NL_BE_4912 = "9fcfd4a14219437a8680117010227a26";

    // NL->BE, Verzending, verzekerd, handtekening
    const PRODUCT_SHIPPING_NL_BE_4914 = "75017f4f16b94b7ebe53e5df0b6ed691";

    // ParcelsEU, is actually 4944, but 4952 should be used
    const PRODUCT_SHIPPING_EU_4952 = "2e00df0d0e7146b5b16f955084792e05";

    // GlobalPack
    const PRODUCT_SHIPPING_GLOBAL_4945 = "2a4dae5c11634f28b8051fbe3d810b2d";
}
