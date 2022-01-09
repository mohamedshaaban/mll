<?php

use App\User;
use Carbon\Carbon;
use TapPayments\GoSell;
if ( ! function_exists( 'getxeroinvoice' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $id
     *
     * @return  float|integer
     */
    function getxeroinvoice( $data ) {

        generatexerotoken();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xero.com/api.xro/2.0/Invoices/'.$data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'xero-tenant-id: '.config('app.XERO_TENANT_ID'),
                'Authorization: Bearer '.session('xero_token'),
                'Accept: application/pdf',
                'Content-Type: application/json',
                'Cookie: _abck=3BE5A6A05BAF5AB8553BA77E30FB8A3F~-1~YAAQnapkXzAvk2N7AQAAORektAYRzy1/LKPj39wbHeduxuuhTgQqQWjHTFBGaFIfXwPiMGD4hSFXFNlSHYV+IAYqvi21kA5YS3/fnu9afTE69mgLnoCtNAQdLhQoIo72RnZ0tLr60szlvZs3eDJ/VuzfICrss3Q0uTfkAqkvpttLZXw1wqz9D4T9sqBKMh6diuIt1c3bjqblM+ZRlP4/SaqyXZLYIp34Bnq94eRPiwIzEiljG5C24UKYYRUQ4MHX52AbW8eZVFLsyqbAn0Qn1lj4LSmkkxhem20wXVaIneuJmrOBYMgOo+QKWCasWo6Uk8IKTNdyb2vMJV40LlCbXej+ZRWjhLlhe86a9ixiIUQHsJ4pn061MYTLnB/WHdV3bdJQtxY=~-1~-1~-1; ak_bmsc=04ED312C21A56171D66FD5D135F64ED3~000000000000000000000000000000~YAAQnapkXzEvk2N7AQAAORektA38VZAMAabM1OhC6Plh5Y5pLJErwb73uHmVLX6BbTUeb8GBk8XMwlvP4suf8017ouzUkdH4Tazn0rBJWLgkOgSQlc4nIHNCinBI4Nxs5CJTgZKzaffB/RTyuZbwWGE4zPvJCoQLYLKT7BMNXcRMEldz32hYMd1vk6+D6slZCW1c/Zu3lSOW/puKticsIDkCtJpNBW0/+LDirr2u71c/LeZbE2arU9Z3H59o9+YubRkWUB6d3CCwMNSMiS5lq4rUFSANuuUQQh262Lrf29fHPxJAerQV9tX7vVHbCzKY3WttTKVFWedkYpnhWnqt5tNXFAs0XFAqlCDlZywWmHEtaLoXmreUw/c=; bm_sv=A1408B6816BDEC3F4D4D6F8596C6278D~o1g1ANuLXSSNgGoDpbs69uAn8nyzQMBQ79S7JINwLx/nOIY3cv3LwXLHMt2yjB8+eAtKdMITNZFnFeYBRef/o9/hFrtctSdnjgHhm6MrSmny/b5WVqdPetoaFcJ0XIkiDw+XZ6fIehNZc9u9kSqY9kVJVx3pLxSNjj+B+VKYitQ=; bm_sz=F3E197CF35509D745E364DF07E089962~YAAQnapkXy8vk2N7AQAAORektA1LcoR92ygBnq7W/AMkk35+XEmGpx9PB7dNw0lhE+p2B92FJRShAGU9mqX7rokkHEWL/jSSb/PTDKuIEVrlkkJ8cNWfFn/DHEEQ2OUivm0ZVAgECgoToph6cO2jB/1+WA9AIWCPpM1qHC+Vll0HeUqxwhbBwjwKI6Nhxg=='
            ),
        ));

        $response = curl_exec($curl);
         curl_close($curl);


        $stringBody = (string) $response;
        $name ='Invoice_'.Carbon::now().'.pdf';
        Storage::disk('local')->put($name, $stringBody);
        return $name;
    }

























}
