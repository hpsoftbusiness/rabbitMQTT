<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
    //logowanie tesla password

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
    //toDo
    //Zrobic pole textarea gdzie wczytuje sie jsona, klika sie guzik i jest generowany xml
}
