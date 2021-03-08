<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Psr\Log\LoggerInterface;
use App\Model\Array2XML;
use Symfony\Component\HttpFoundation\Request;

class MqttConnectorController extends AbstractController
{
    CONST SERVER = 'localhost';
    CONST PORT = '8883';
    CONST CLIENT_ID = 'test';
    CONST MESSAGE = 'testMessage';
    CONST TOPIC = '/#';
    CONST TLS_CERTIFICATE_AUTHORITY_FILE = 'result/ca_certificate.pem';
    CONST TLS_CLIENT_CERTIFICATE_FILE = 'result/client_certificate.pem';
    CONST TLS_CLIENT_CERTIFICATE_KEY_FILE = 'result/client_key_unencrypted.pem';

    /**
     * @Route("/mqtt/publisher", name="mqtt_publisher")
     */
    public function publisher(LoggerInterface $logger): Response
    {
        $server = self::SERVER;
        $port = self::PORT;
        $clientId = self::CLIENT_ID;
        $message = self::MESSAGE;
        $topic = self::TOPIC;

        $tlsCertificateAuthorityFile = self::TLS_CERTIFICATE_AUTHORITY_FILE;
        $tlsClientCertificateFile = self::TLS_CLIENT_CERTIFICATE_FILE;
        $tlsClientCertificateKeyFile = self::TLS_CLIENT_CERTIFICATE_KEY_FILE;

        $mqtt = new MqttClient($server, $port, $clientId);
        $connectionSettings = (new ConnectionSettings)
            ->setUseTls(true)
            ->setTlsCertificateAuthorityFile($tlsCertificateAuthorityFile)
            ->setTlsClientCertificateFile($tlsClientCertificateFile)
            ->setTlsClientCertificateKeyFile($tlsClientCertificateKeyFile);
        $mqtt->connect($connectionSettings);
        $mqtt->publish($topic, $message);
        $logger->info('topic:' . $topic . ',message:' . $message);
        $mqtt->disconnect();

        return new Response(json_encode(true));
    }

    /**
     * @Route("/mqtt/subscriber", name="mqtt_subscriber")
     */
    public function subscriber(LoggerInterface $logger): Response
    {
        $server = self::SERVER;
        $port = self::PORT;
        $clientId = self::CLIENT_ID;
        $topic = self::TOPIC;
        $tlsCertificateAuthorityFile = self::TLS_CERTIFICATE_AUTHORITY_FILE;
        $tlsClientCertificateFile = self::TLS_CLIENT_CERTIFICATE_FILE;
        $tlsClientCertificateKeyFile = self::TLS_CLIENT_CERTIFICATE_KEY_FILE;

        $mqtt = new MqttClient($server, $port, $clientId);
        $connectionSettings = (new ConnectionSettings)
            ->setUseTls(true)
            ->setUseTls(true)
            ->setTlsCertificateAuthorityFile($tlsCertificateAuthorityFile)
            ->setTlsClientCertificateFile($tlsClientCertificateFile)
            ->setTlsClientCertificateKeyFile($tlsClientCertificateKeyFile);
        $mqtt->connect($connectionSettings);
        $mqtt->subscribe($topic, function ($topic, $message) use ($mqtt, $logger) {
            $logger->info('topic:' . $topic . ',message:' . $message);
        }, 0);
        $mqtt->loop(true);
        $mqtt->disconnect();

        return new Response(json_encode(true));
    }

    /**
     * @Route("/mqtt/jsonentry", name="json_entry")
     */
    public function jsonEntry()
    {
        return $this->render('mqtt_connector/jsonentry.html', []);
    }

    /**
     * @Route("/mqtt/parsejson", name="parse_json")
     */
    public function parseJson(Request $request)
    {
        $json = $request->get('json');
        $xml = new Array2XML();
        $output = $xml->parse($json);

        return new Response($output, 200);
    }

    /**
     * @Route("/mqtt/charts", name="mqtt_charts")
     */
    public function charts()
    {
        return $this->render('mqtt_connector/charts.html.twig', []);
    }
}