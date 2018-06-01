<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Log\LoggerInterface;


class SpiderController extends Controller
{

    /**
     * @Route("/spider", name="spider")
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     */
    public function index(LoggerInterface $logger)
    {
        $logger->info('I just got the logger');
        return new JsonResponse( array( "RESP" => "OK",
                                 ) );
    }

    /**
     * @Route("/spider/subentzioak", name="spider_subentzioak")
     */
    public function subentzioak()
    {
        $headers = [
            'headers' => [
                'Accept-Encoding'           => 'gzip, deflate',
                'Host'                      => 'www.pap.minhafp.gob.es',
                'Accept-Language'           => 'es,eu;q=0.9,en-GB;q=0.8,en;q=0.7',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent'                => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.162 Safari/537.36',
                'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Cache-Control'             => 'no-cache',
                'Referer'                   => 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/convocatorias',
                'DNT'                       => 1,
            ],
            'cookies'   => true,
            'debug'     => false
        ];

        $client = new Client($headers);
        try {
            $client->request( 'GET', 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/convocatorias' );
        } catch ( GuzzleException $e ) {
            echo $e->getMessage();
        }

        $post_data = array(
            "_ministerios"       => "1",
            "_organos"           => "1",
            "_cAutonomas"        => "1",
            "_departamentos"     => "1",
            "administracion"     => "locales",
            "entLocSearch"       => "pasaia",
            "locales"            => "3532",
            "_locales"           => "1",
            "localesOculto"      => "3532",
            "_localesOculto"     => "1",
            "beneficiarioFilter" => "DNI",
            "beneficiarioDNI"    => "",
            "beneficiarioNombre" => "",
            "beneficiario"       => "",
            "fecDesde"           => "",
            "fecHasta"           => "",
            "titulo"             => "",
            "_regiones"          => "1",
            "_actividadesNACE"   => "1",
            "_instrumentos"      => "1",
        );

        $client->post(
            'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/convocatorias', array (
            'form_params' => $post_data
        ));

        $cookieJar = $client->getConfig('cookies');
        $gaileta = $cookieJar->toArray();
        $nireCookie = $this->parse_cookie( $gaileta );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt( $ch, CURLOPT_URL, 'http://www.pap.minhafp.gob.es/bdnstrans/busqueda?type=convs&_search=false&nd=1521189224961&rows=50&page=1&sidx=4&sord=desc' );

        $kabezerak = [
            'Pragma'                    => 'no-cache',
            'Accept-Encoding'           => 'gzip, deflate',
            'Host'                      => 'www.pap.minhafp.gob.es',
            'Accept-Language'           => 'es,eu;q=0.9,en-GB;q=0.8,en;q=0.7',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent'                => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.162 Safari/537.36',
            'Accept'                    => 'application/json, text/javascript, */*; q=0.01',
            'Cache-Control'             => 'no-cache',
            'Referer'                   => 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/convocatorias',
            'X-Requested-With'          => 'XMLHttpRequest',
            'DNT'                       => 1,
            'Connection'                => 'keep-alive'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $kabezerak);
        curl_setopt( $ch, CURLOPT_COOKIE, $nireCookie );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        $erantzuna = curl_exec( $ch );
        curl_close( $ch );

        $data = json_decode( $erantzuna, true );
        $arr = array();

        if ( array_key_exists("rows", $data)) {
            foreach ($data["rows"] as $a) {
                $temp = array(
                    'id'                => $a[ 0 ],
                    'administracion'    => $a[ 1 ],
                    'departamento'      => $a[ 2 ],
                    'Organo'            => $a[ 3 ],
                    'FechaRegistro'     => $a[ 4 ],
                    'TituloConvocatoria'=> $a[ 5 ],
                    'BBReguladoras'     => $a[ 6 ],
                    'IDBDNS'            => $a[ 7 ],

                );
                array_push( $arr, $temp );
            }

            $response =new JsonResponse( $arr );
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');

            return $response;
        }

        return new JsonResponse( array( 'data' => 'No data',) );
    }

    /**
     * @Route("/spider/konzesioak", name="spider_konzesioak")
     */
    public function konzesioak()
    {
        $headers = [
            'headers' => [
                'Accept-Encoding'           => 'gzip, deflate',
                'Host'                      => 'www.pap.minhafp.gob.es',
                'Accept-Language'           => 'es,eu;q=0.9,en-GB;q=0.8,en;q=0.7',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent'                => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.162 Safari/537.36',
                'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Cache-Control'             => 'no-cache',
                'Referer'                   => 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/concesiones',
                'DNT'                       => 1,
            ],
            'cookies'   => true,
            'debug'     => false
        ];

        // Lehen orria
        /** @var Client $client */
        $client = new Client($headers);
        try {
            $client->request( 'GET', 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/concesiones' );
        } catch ( GuzzleException $e ) {
            echo $e->getMessage();
        }

        $post_data = array(
            "_ministerios"       => "1",
            "_organos"           => "1",
            "_cAutonomas"        => "1",
            "_departamentos"     => "1",
            "administracion"     => "locales",
            "entLocSearch"       => "pasaia",
            "locales"            => "3532",
            "_locales"           => "1",
            "localesOculto"      => "3532",
            "_localesOculto"     => "1",
            "beneficiarioFilter" => "DNI",
            "beneficiarioDNI"    => "",
            "beneficiarioNombre" => "",
            "beneficiario"       => "",
            "fecDesde"           => "",
            "fecHasta"           => "",
            "titulo"             => "",
            "_regiones"          => "1",
            "_actividadesNACE"   => "1",
            "_instrumentos"      => "1",
        );

        $client->post(
            'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/concesiones', array (
            'form_params' => $post_data
        ));

        $cookieJar = $client->getConfig('cookies');
        $gaileta = $cookieJar->toArray();
        $nireCookie = $this->parse_cookie( $gaileta );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt( $ch, CURLOPT_URL, 'http://www.pap.minhafp.gob.es/bdnstrans/busqueda?type=concs&_search=false&nd=1521466639104&rows=50&page=1&sidx=8&sord=asc' );

        $kabezerak = [
            'Pragma'                    => 'no-cache',
            'Accept-Encoding'           => 'gzip, deflate',
            'Host'                      => 'www.pap.minhafp.gob.es',
            'Accept-Language'           => 'es,eu;q=0.9,en-GB;q=0.8,en;q=0.7',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent'                => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.162 Safari/537.36',
            'Accept'                    => 'application/json, text/javascript, */*; q=0.01',
            'Cache-Control'             => 'no-cache',
            'Referer'                   => 'http://www.pap.minhafp.gob.es/bdnstrans/GE/es/concesiones',
            'X-Requested-With'          => 'XMLHttpRequest',
            'DNT'                       => 1,
            'Connection'                => 'keep-alive'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $kabezerak);
        curl_setopt( $ch, CURLOPT_COOKIE, $nireCookie );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        $erantzuna = curl_exec( $ch );
        curl_close( $ch );

        $data = json_decode( $erantzuna, true );
        $arr = array();

        $em = $this->getDoctrine()->getManager();

        if ( array_key_exists("rows", $data)) {
            if ($data["rows"] !== null) {
                foreach ($data["rows"] as $a) {
                    $temp = array(
                        'administracion'            => $a[ 2 ],
                        'departamento'              => $a[ 3 ],
                        'Organo'                    => $a[ 4 ],
                        'TituloConvocatoria'        => $a[ 5 ],
                        'BBReguladoras'             => $a[ 6 ],
                        'AplicacionPresupuestaria'  => $a[ 7 ],
                        'FechaConcesion'            => $a[ 8 ],
                        'Beneficiario'              => $a[ 9 ],
                        'Importe'                   => $a[ 10 ],
                        'Instrumento'               => $a[ 11 ],
                        'AyudaEquivalente'          => $a[ 12 ],
                        'Detalles'                  => $a[ 13 ],
                    );
                    array_push( $arr, $temp );
                }
                $em->flush();
            }

            $response =new JsonResponse( $arr );
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');

            return $response;
        }

        return new JsonResponse( array( 'data' => 'No data',) );
    }


    function parse_cookie($gaileta) {

        $nireCookie ="";
        $lehena = 1;

        foreach ($gaileta as $key => $val) {

            if ($lehena == 1) {
                $nireCookie .= $val["Name"]."=".$val["Value"];
                $lehena = 0;
            } else {
                $nireCookie .= "; ".$val["Name"]."=".$val["Value"];
            }
        }
        $var1 = "JSESSIONID=".$gaileta[0]["Value"].";";
        $resp = $var1;
        if (count($gaileta)>1) {
            $var2 = " TS01358f12=".$gaileta[1]["Value"].";";
            $var3 = " TS0181fda2=".$gaileta[2]["Value"];
            $resp = $resp . $var2 . $var3;
        }


        return $resp;
    }
}
