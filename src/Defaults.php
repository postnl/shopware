<?php declare(strict_types=1);

namespace PostNL\Shopware6;

class Defaults
{
    //region Constants
    //region Custom Fields
    const CUSTOM_FIELDS_KEY = 'postnl';
    const CUSTOM_FIELDS_STREETNAME_KEY = 'streetName';
    const CUSTOM_FIELDS_HOUSENUMBER_KEY = 'houseNumber';
    const CUSTOM_FIELDS_HOUSENUMBER_ADDITION_KEY = 'houseNumberAddition';
    const CUSTOM_FIELDS_TIMEFRAME_KEY = 'timeframe';
    const CUSTOM_FIELDS_DELIVERY_DATE_KEY = 'deliveryDate';
    const CUSTOM_FIELDS_SENT_DATE_KEY = 'sentDate';
    //endregion
    //region Zone
    const ZONE_ONLY_EUROPE = "PostNL zone only Europe";
    const ZONE_ONLY_BELGIUM = "PostNL zone only Belgium";
    const ZONE_ONLY_REST_OF_WORLD = "PostNL zone only rest of world";
    const ZONE_ONLY_NETHERLANDS = "PostNL zone only Netherlands";
    //endregion
    //region Lineitem
    const LINEITEM_PAYLOAD_WEIGHT_KEY = 'weight';
    const LINEITEM_PAYLOAD_TARIFF_KEY = 'hsTariffCode';
    const LINEITEM_PAYLOAD_ORIGIN_KEY = 'countryOfOrigin';
    //endregion
    //endregion

    //region Category Defaults
    /**
     * These act as the default for their category
     */
    const PRODUCT_SHIPMENT_NL_NL = self::PRODUCT_SHIPMENT_NL_NL_3085;
    const PRODUCT_PICKUP_NL_NL   = self::PRODUCT_PICKUP_NL_NL_3533;
    const PRODUCT_MAILBOX_NL_NL  = self::PRODUCT_MAILBOX_NL_NL_2928;

    const PRODUCT_SHIPMENT_NL_BE = self::PRODUCT_SHIPMENT_NL_BE_4946;
    const PRODUCT_PICKUP_NL_BE   = self::PRODUCT_PICKUP_NL_BE_4936;

    const PRODUCT_SHIPMENT_NL_EU = self::PRODUCT_SHIPMENT_NL_EU_4907_005_025;
    const PRODUCT_MAILBOX_NL_EU = self::PRODUCT_MAILBOX_NL_EU_6440;
    const PRODUCT_PARCEL_NL_EU  = self::PRODUCT_PARCEL_NL_EU_6405;

    const PRODUCT_SHIPMENT_NL_GLOBAL = self::PRODUCT_SHIPMENT_NL_GLOBAL_4909_005_025;
    const PRODUCT_MAILBOX_NL_GLOBAL = self::PRODUCT_MAILBOX_NL_GLOBAL_6440;
    const PRODUCT_PARCEL_NL_GLOBAL  = self::PRODUCT_PARCEL_NL_GLOBAL_6405;

    const PRODUCT_SHIPMENT_BE_BE = self::PRODUCT_SHIPMENT_BE_BE_4960;
    const PRODUCT_PICKUP_BE_BE   = self::PRODUCT_PICKUP_BE_BE_4880;

    const PRODUCT_SHIPMENT_BE_NL = self::PRODUCT_SHIPMENT_BE_NL_4890;
    const PRODUCT_PICKUP_BE_NL   = self::PRODUCT_PICKUP_BE_NL_4898;

    const PRODUCT_SHIPMENT_BE_EU = self::PRODUCT_SHIPMENT_BE_EU_4907_005_025;
    const PRODUCT_SHIPMENT_BE_GLOBAL = self::PRODUCT_SHIPMENT_BE_GLOBAL_4909_005_025;
    //endregion

    //region V1.0 Identifiers
    /**
     * V1.0
     */
    // NL->NL, Verzending
    const PRODUCT_SHIPMENT_NL_NL_3085 = "01c8aeac08cd4d1b95de9ef6a18ae89d";

    // NL->NL, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_NL_NL_3385 = "6aa1d2225d724416bea415e2454de832";

    // NL->NL, Verzending, retour b.g.g.
    const PRODUCT_SHIPMENT_NL_NL_3090 = "ed587b9fa00b421f94e98a0c85df4124";

    // NL->NL, Verzending, niet bij buren bezorgen, retour b.g.g.
    const PRODUCT_SHIPMENT_NL_NL_3390 = "79120b803f6d46e29c300ab46d695c6e";

    // NL->NL, Verzending, verzekerd
    const PRODUCT_SHIPMENT_NL_NL_3087 = "70c07296123e463cbb19cedfad9c6f02";

    // NL->NL, Verzending, verzekerd, retour b.g.g.
    const PRODUCT_SHIPMENT_NL_NL_3094 = "ad98fc8e8165466eb5485edcfdaa6b6c";

    // NL->NL, Verzending, handtekening, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_NL_NL_3089 = "6b4a2b10b13e4b44a7a4db94d8f4736e";

    // NL->NL, Verzending, handtekening, niet bij buren bezorgen, retour b.g.g.
    const PRODUCT_SHIPMENT_NL_NL_3096 = "75583df93af54e368449cac960089142";

    // NL->NL, Verzending, handtekening
    const PRODUCT_SHIPMENT_NL_NL_3189 = "ebee6dfe35394d429c3126842f391183";

    // NL->NL, Verzending, handtekening, retour b.g.g.
    const PRODUCT_SHIPMENT_NL_NL_3389 = "8cca6f28f4fd421a8128645266dc780a";

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

    // NL->BE, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_NL_BE_4941 = "00dfac1c0dbd4b7480dc519e864e04b0";

    // NL->BE, Verzending
    const PRODUCT_SHIPMENT_NL_BE_4946 = "7f68f71067214ad4899862d87ac85950";

    // NL->BE, Verzending, handtekening
    const PRODUCT_SHIPMENT_NL_BE_4912 = "9fcfd4a14219437a8680117010227a26";

    // NL->BE, Verzending, verzekerd, handtekening
    const PRODUCT_SHIPMENT_NL_BE_4914 = "75017f4f16b94b7ebe53e5df0b6ed691";

    // NL->BE, Pickup
    const PRODUCT_PICKUP_NL_BE_4936 = "5a1d77cf89ec416f8c92f440ba961c6b";

    // NL->ParcelsEU, is actually 4944, but 4952 should be used.
    const PRODUCT_SHIPMENT_NL_EU_4952 = "2e00df0d0e7146b5b16f955084792e05";

    // NL->GlobalPack
    const PRODUCT_SHIPMENT_NL_GLOBAL_4945 = "2a4dae5c11634f28b8051fbe3d810b2d";
    //endregion

    //region V1.1 Identifiers
    /**
     * V1.1
     */
    // BE->BE, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_BE_BE_4960 = "85195fa94d6a4c50a2ad2ab579820d2b";

    // BE->BE, Verzending
    const PRODUCT_SHIPMENT_BE_BE_4961 = "15a561ba00a8441f8e791080354d1d51";

    // BE->BE, Verzending, niet bij buren bezorgen, handtekening voor ontvangst
    const PRODUCT_SHIPMENT_BE_BE_4962 = "32eda40217554fd2a27eab564d6330ef";

    // BE->BE, Verzending, handtekening voor ontvangst
    const PRODUCT_SHIPMENT_BE_BE_4963 = "8262bd7d13f84ddf8bf764c86910f1af";

    // BE->BE, Verzending, niet bij buren bezorgen, verzekerd
    const PRODUCT_SHIPMENT_BE_BE_4965 = "76128ef0531645d4a93979f7e58be30f";

    // BE->BE, Pickup, verzekerd
    const PRODUCT_PICKUP_BE_BE_4878 = "7108d13a1c104ec3a3b0b661c9436186";

    // BE->BE, Pickup
    const PRODUCT_PICKUP_BE_BE_4880 = "674c471cdc524898a9c503bf5995447b";

    // BE->ParcelsEU
    const PRODUCT_SHIPMENT_BE_EU_4952 = "12ee17b34fcb4808997f381dec3bd521";

    // BE->GlobalPack
    const PRODUCT_SHIPMENT_BE_GLOBAL_4945 = "30914ec3db4749f1b050de8f0a12a20b";
    //endregion

    //region V2.0 Identifiers
    /**
     * V2.0
     */
    // Avondbezorging
    const OPTION_118_006 = "846dedd4b74e41c8b4d15b41fec6e166";

    //region International Products 2023
    // NL->Parcel EU
    const PRODUCT_SHIPMENT_NL_EU_4907_005_025 = "cc790c0fdc014d4abc1fd8e8fea04d2d";
    const PRODUCT_SHIPMENT_NL_EU_4907_004_015 = "a0cd6266f7af4827a5c4f992fbbe018e";
    const PRODUCT_SHIPMENT_NL_EU_4907_004_016 = "9a363d644cb04f2992b11532ca3423a1";

    // BE->Parcel EU
    const PRODUCT_SHIPMENT_BE_EU_4907_005_025 = "882d315b86834c3db97b3b44c817ed28";
    const PRODUCT_SHIPMENT_BE_EU_4907_004_015 = "aca0c58bb0c3458aa5cc73bf9b1193f5";
    const PRODUCT_SHIPMENT_BE_EU_4907_004_016 = "d82b87a2c08a4c58aab43d634129d164";

    // NL->Parcel non-EU (GlobalPack)
    const PRODUCT_SHIPMENT_NL_GLOBAL_4909_005_025 = "918bb3b30a8c402c9f089672238e3d10";
    const PRODUCT_SHIPMENT_NL_GLOBAL_4909_004_015 = "243372140df14282a33593aba308939d";
    const PRODUCT_SHIPMENT_NL_GLOBAL_4909_004_016 = "b0e76c782e1a412f803009ec7e70f66c";

    // BE->Parcel non-EU (GlobalPack)
    const PRODUCT_SHIPMENT_BE_GLOBAL_4909_005_025 = "6b9edb204ab24ac296eaed5b6d02f4dd";
    const PRODUCT_SHIPMENT_BE_GLOBAL_4909_004_015 = "b7faaef178e2496c8375b172fc7045b1";
    const PRODUCT_SHIPMENT_BE_GLOBAL_4909_004_016 = "7c4fdd5ea8224573ab477fe7493939d0";

    // -> ParcelEU Required
    const OPTION_101_012 = "58c6922504bd427cb58fec5631e5d3ad";

    // Track and Trace Uninsured
    const OPTION_005_025 = "8f18b46a4a0f446690de85ea236ebb0d";

    // Track and Trace Insured
    const OPTION_004_015 = "b7d9d225aa25420ba3beb559422aca0b";

    // Track and Trace Insured Plus
    const OPTION_004_016 = "de9571485773424d91a036a9b90b8018";
    //endregion

    //region New Mailbox parcels
    // Boxable Packet NL->EU
    const PRODUCT_MAILBOX_NL_EU_6440 = "34375f43be67429faa94ef2449f79762";

    // Boxable Packet Track and Trace NL->EU
    const PRODUCT_MAILBOX_NL_EU_6972 = "092a6a11729b44db981903e292d0c67d";

    // Boxable Packet NL->GLOBAL
    const PRODUCT_MAILBOX_NL_GLOBAL_6440 = "daa270d50c074289bc57bfbd9f6e3a3d";

    // Boxable Packet Track and Trace NL->GLOBAL
    const PRODUCT_MAILBOX_NL_GLOBAL_6972 = "519069ee8027453ebc097817ee46b287";
    //endregion

    //region New EU/Global package type identifiers
    // These were first labeled as Mailbox packages, but are actually a new international type.

    // Parcel NL->EU
    const PRODUCT_PARCEL_NL_EU_6405 = "02388c7bd54245bc919b04752821fbc2";

    // Parcel Track and Trace NL->EU
    const PRODUCT_PARCEL_NL_EU_6350 = "5cc233a0e410421f819f9351acc659c3";

    // Parcel Track and Trace Insurance NL->EU
    const PRODUCT_PARCEL_NL_EU_6906 = "5224fb01bae44f4aae2c0f3967e0485e";

    // Parcel NL->GLOBAL
    const PRODUCT_PARCEL_NL_GLOBAL_6405 = "7fe60652bc474895b9c5c3de36ec8d89";

    // Parcel Track and Trace NL->GLOBAL
    const PRODUCT_PARCEL_NL_GLOBAL_6350 = "69f41991990e47b58ddc0dc7991843ba";

    // Parcel Track and Trace Insurance NL->GLOBAL
    const PRODUCT_PARCEL_NL_GLOBAL_6906 = "b9ee042530ce4009a82671c52362b51b";
    //endregion
    //endregion

    //region V3.1.0 Identifiers
    /**
     * V3.1.0
     */

    //region New BE -> NL productcodes

    // BE->NL, Verzending
    const PRODUCT_SHIPMENT_BE_NL_4890 = "45432c40e63f4ec79aa230c0bed4c0e2";

    // BE->NL, Verzending, handtekening
    const PRODUCT_SHIPMENT_BE_NL_4891 = "6014c5955e0c448ab27c37142f0bfdac";

    // BE->NL, Verzending, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_BE_NL_4893 = "be49eed381bc4b7e935a39926273717c";

    // BE->NL, Verzending, handtekening, niet bij buren bezorgen
    const PRODUCT_SHIPMENT_BE_NL_4894 = "47ab691cd2504280b1af6e6e002c94f2";

    // BE->NL, Verzending, handtekening, retour b.g.g.
    const PRODUCT_SHIPMENT_BE_NL_4896 = "dac322ddf7db4e3c95e7ba915b21f15c";

    // BE->NL, Verzending, verzekerd, handtekening
    const PRODUCT_SHIPMENT_BE_NL_4897 = "2cc40e192141468cb57852305596ad37";

    // BE->NL, Pickup, handtekening
    const PRODUCT_PICKUP_BE_NL_4898 = "bc5f4a5e53df412b80600eaa68d7d335";
    //endregion
    //endregion

    //region Placeholder identifiers
//    const PLACEHOLDER = "aa67b151850646849464e633d4a14beb";
//    const PLACEHOLDER = "91cca8b821f4413dad3f3133b7b5dffe";
//    const PLACEHOLDER = "596c345d25684e729909175ed50c9897";
//    const PLACEHOLDER = "cbd12ab384534364919140e09f1c77bb";
//    const PLACEHOLDER = "01d9979b809b4458b0e9ab16051a3570";
//    const PLACEHOLDER = "28834a4946a04bad9b82051c5318531d";
//    const PLACEHOLDER = "145be8d723fd4410b06a95993a076e63";
//    const PLACEHOLDER = "59faf97383fb41c9ba18fde481999335";
//    const PLACEHOLDER = "6df10bb508e340a4b96975180e1b8f50";
//    const PLACEHOLDER = "1658038211964ac381e8d56fd6726a9c";
//    const PLACEHOLDER = "a1681c8201ef4a97b56bd610093b7d9e";
//    const PLACEHOLDER = "b0a1e3ccfa5942b3831cdc52ca2ab798";
//    const PLACEHOLDER = "8e34000dbe6940dda727d8814b848c01";
//    const PLACEHOLDER = "abd527fd3c41474294290a6af9681484";
//    const PLACEHOLDER = "04a0bde8e9694d7c891ef51a2fda562f";
//    const PLACEHOLDER = "a8c8d14ce43d408c81f48a32ad1d6342";
//    const PLACEHOLDER = "625dc4cdf7f44742a86e428a75aceb11";
//    const PLACEHOLDER = "d9fb416a86754c559edbdee5e0c40fe1";
//    const PLACEHOLDER = "12cef6d9e7c246c9a93851c91b9fe5e6";
//    const PLACEHOLDER = "d7958f6b6f564ed8ab2c47aa66b2df53";
//    const PLACEHOLDER = "e75ffb02bdc24976a325b96bfeb886d4";
//    const PLACEHOLDER = "b057103ade454503adcab1fc72fda0a5";
//    const PLACEHOLDER = "54d232c29ede41508130f2e7ae159f41";
//    const PLACEHOLDER = "a2bbb26acf0e404cb660c86e1d308717";
//    const PLACEHOLDER = "add8da95fea2484ea686461fe9f9c5e4";
//    const PLACEHOLDER = "0c16fb2b535b4f4f9293a10418e2c1a4";
//    const PLACEHOLDER = "b8d2202a26a44b0dbe916e95daf0d45f";
//    const PLACEHOLDER = "bfb2b3af07304a248ef2586988acedea";
//    const PLACEHOLDER = "44e66dae80764307937a1e8926ddf29a";
//    const PLACEHOLDER = "1213cf4ae91b469198656753aec830da";
//    const PLACEHOLDER = "08232e436d4541e1b25db681157ced70";
//    const PLACEHOLDER = "d5b05d2799594aa9aa872aa2430a27dc";
//    const PLACEHOLDER = "fe38c17b41cc420c9af34dec308080c4";
//    const PLACEHOLDER = "4b6f788e034c4900b44fdefe98031b27";
//    const PLACEHOLDER = "d3c7414a99d2447e859588894d9d75be";
    //endregion

    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
