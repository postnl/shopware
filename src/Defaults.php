<?php declare(strict_types=1);

namespace PostNL\Shopware6;

class Defaults
{
    const CUSTOM_FIELDS_KEY = 'postnl';
    const CUSTOM_FIELDS_STREETNAME_KEY = 'streetName';
    const CUSTOM_FIELDS_HOUSENUMBER_KEY = 'houseNumber';
    const CUSTOM_FIELDS_HOUSENUMBER_ADDITION_KEY = 'houseNumberAddition';
    const CUSTOM_FIELDS_TIMEFRAME_KEY = 'timeframe';
    const CUSTOM_FIELDS_DELIVERY_DATE_KEY = 'deliveryDate';
    const CUSTOM_FIELDS_SENT_DATE_KEY = 'sentDate';

    const ZONE_ONLY_EUROPE = "PostNL zone only Europe";
    const ZONE_ONLY_BELGIUM = "PostNL zone only Belgium";
    const ZONE_ONLY_REST_OF_WORLD = "PostNL zone only rest of world";
    const ZONE_ONLY_NETHERLANDS = "PostNL zone only Netherlands";

    const LINEITEM_PAYLOAD_WEIGHT_KEY = 'weight';
    const LINEITEM_PAYLOAD_TARIFF_KEY = 'hsTariffCode';
    const LINEITEM_PAYLOAD_ORIGIN_KEY = 'countryOfOrigin';

    const PRODUCT_MAILBOX_NL_NL = self::PRODUCT_MAILBOX_NL_NL_2928;
    const PRODUCT_SHIPPING_NL_NL = self::PRODUCT_SHIPPING_NL_NL_3085;
    const PRODUCT_PICKUP_NL_NL = self::PRODUCT_PICKUP_NL_NL_3533;

    const PRODUCT_SHIPPING_NL_BE = self::PRODUCT_SHIPPING_NL_BE_4946;
    const PRODUCT_PICKUP_NL_BE = self::PRODUCT_PICKUP_NL_BE_4936;

    const PRODUCT_SHIPPING_BE_BE = self::PRODUCT_SHIPPING_BE_BE_4960;
    const PRODUCT_PICKUP_BE_BE = self::PRODUCT_PICKUP_BE_BE_4880;

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

    // NL->ParcelsEU, is actually 4944, but 4952 should be used.
    const PRODUCT_SHIPPING_NL_EU_4952 = "2e00df0d0e7146b5b16f955084792e05";

    // NL->GlobalPack
    const PRODUCT_SHIPPING_NL_GLOBAL_4945 = "2a4dae5c11634f28b8051fbe3d810b2d";

    /**
     * V1.1
     */
    // BE->BE, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPPING_BE_BE_4960 = "85195fa94d6a4c50a2ad2ab579820d2b";

    // BE->BE, Verzending
    const PRODUCT_SHIPPING_BE_BE_4961 = "15a561ba00a8441f8e791080354d1d51";

    // BE->BE, Verzending, niet bij buren bezorgen, handtekening voor ontvangst
    const PRODUCT_SHIPPING_BE_BE_4962 = "32eda40217554fd2a27eab564d6330ef";

    // BE->BE, Verzending, handtekening voor ontvangst
    const PRODUCT_SHIPPING_BE_BE_4963 = "8262bd7d13f84ddf8bf764c86910f1af";

    // BE->BE, Verzending, niet bij buren bezorgen, verzekerd
    const PRODUCT_SHIPPING_BE_BE_4965 = "76128ef0531645d4a93979f7e58be30f";

    // BE->BE, Pickup,verzekerd
    const PRODUCT_PICKUP_BE_BE_4878 = "7108d13a1c104ec3a3b0b661c9436186";

    // BE->BE, Pickup
    const PRODUCT_PICKUP_BE_BE_4880 = "674c471cdc524898a9c503bf5995447b";

    // BE->ParcelsEU
    const PRODUCT_SHIPPING_BE_EU_4952 = "12ee17b34fcb4808997f381dec3bd521";

    // BE->GlobalPack
    const PRODUCT_SHIPPING_BE_GLOBAL_4945 = "30914ec3db4749f1b050de8f0a12a20b";

//    const PLACEHOLDER = "846dedd4b74e41c8b4d15b41fec6e166";
//    const PLACEHOLDER = "cc790c0fdc014d4abc1fd8e8fea04d2d";
//    const PLACEHOLDER = "882d315b86834c3db97b3b44c817ed28";
//    const PLACEHOLDER = "918bb3b30a8c402c9f089672238e3d10";
//    const PLACEHOLDER = "6b9edb204ab24ac296eaed5b6d02f4dd";
//    const PLACEHOLDER = "58c6922504bd427cb58fec5631e5d3ad";
//    const PLACEHOLDER = "8f18b46a4a0f446690de85ea236ebb0d";
//    const PLACEHOLDER = "b7d9d225aa25420ba3beb559422aca0b";
//    const PLACEHOLDER = "de9571485773424d91a036a9b90b8018";
//    const PLACEHOLDER = "a0cd6266f7af4827a5c4f992fbbe018e";
//    const PLACEHOLDER = "9a363d644cb04f2992b11532ca3423a1";
//    const PLACEHOLDER = "aca0c58bb0c3458aa5cc73bf9b1193f5";
//    const PLACEHOLDER = "d82b87a2c08a4c58aab43d634129d164";
//    const PLACEHOLDER = "243372140df14282a33593aba308939d";
//    const PLACEHOLDER = "b0e76c782e1a412f803009ec7e70f66c";
//    const PLACEHOLDER = "b7faaef178e2496c8375b172fc7045b1";
//    const PLACEHOLDER = "7c4fdd5ea8224573ab477fe7493939d0";
//    const PLACEHOLDER = "34375f43be67429faa94ef2449f79762";
//    const PLACEHOLDER = "092a6a11729b44db981903e292d0c67d";

}
