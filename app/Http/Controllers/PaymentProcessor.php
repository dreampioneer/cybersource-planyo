<?php
namespace App\Http\Controllers;

require_once 'Resources/ExternalConfiguration.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use CyberSource\Model\Ptsv2paymentsPaymentInformation;
use CyberSource\Model\Ptsv2paymentsPaymentInformationCard;
use CyberSource\Model\Ptsv2paymentsOrderInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails;
use CyberSource\Model\Ptsv2paymentsOrderInformationBillTo;
use CyberSource\Model\CreatePaymentRequest;
use CyberSource\Model\Ptsv2paymentsClientReferenceInformation;
use CyberSource\Model\Ptsv2paymentsProcessingInformation;
use CyberSource\Model\Ptsv2paymentsidcapturesOrderInformationAmountDetails;
use CyberSource\Model\Ptsv2paymentsidcapturesOrderInformation;
use CyberSource\Api\CaptureApi;
use CyberSource\Model\CapturePaymentRequest;
use CyberSource\ApiClient;
use CyberSource\ExternalConfiguration;
use CyberSource\Api\PaymentsApi;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;

class PaymentProcessor
{
    private $client;
    private $api_key;

    public function __construct()
    {
        $this->client = new Client();
        $this->api_key = env('PLANYO_API_KEY', default: '');
    }

    private function sendApiRequest($url, $headers)
    {
        try {
            $request = new \GuzzleHttp\Psr7\Request('GET', $url, $headers);
            $response = $this->client->sendAsync($request)->wait();
            $apiResponse = json_decode($response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from API');
            }
            return $apiResponse;
        } catch (RequestException $e) {
            return ['response_code' => 3, 'response_message' => $e->getMessage()];
        } catch (Exception $e) {
            return ['response_code' => 3, 'response_message' => $e->getMessage()];
        }
    }

    public function makePayment($data)
    {
        $capture = isset($data['flag']) && $data['flag'] == "true";

        // Prepare payment details
        $paymentInfo = new Ptsv2paymentsPaymentInformation([
            'card' => new Ptsv2paymentsPaymentInformationCard([
                'number' => $data['number'],
                'expirationMonth' => $data['expirationMonth'],
                'expirationYear' => $data['expirationYear']
            ])
        ]);
        $orderInfo = new Ptsv2paymentsOrderInformation([
            'amountDetails' => new Ptsv2paymentsOrderInformationAmountDetails([
                'totalAmount' => array_sum($data['totalAmount']),
                'currency' => $data['currency']
            ]),
            'billTo' => new Ptsv2paymentsOrderInformationBillTo([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'address1' => '10 Ang Mo Kio Street 65 #03-13 Techpoint',
                'locality' => $data['country'],
                'administrativeArea' => $data['country'],
                'postalCode' => '569059',
                'country' => $data['country'],
                'email' => $data['email'],
                'phoneNumber' => $data['phoneNumber']
            ])
        ]);

        // Build the payment request object
        $requestObj = new CreatePaymentRequest([
            'clientReferenceInformation' => new Ptsv2paymentsClientReferenceInformation([
                'code' => $data['reservationIds'][0]
            ]),
            'processingInformation' => new Ptsv2paymentsProcessingInformation([
                'capture' => $capture
            ]),
            'paymentInformation' => $paymentInfo,
            'orderInformation' => $orderInfo
        ]);

        $api_client = new ApiClient(
            (new ExternalConfiguration())->ConnectionHost(),
            (new ExternalConfiguration())->merchantConfigObject()
        );
        $api_instance = new PaymentsApi($api_client);

        try {
            $apiResponse = $api_instance->createPayment($requestObj);
            $paymentStatus = $this->getPaymentStatus($apiResponse[0]->getStatus());
            Log::info($apiResponse[0]->getStatus());
            Log::info($paymentStatus);
            Reservation::whereIn('reservation_id', $data['reservationIds'])->update([
                'payment_confirming_reservation' => $paymentStatus, 'captured' => $capture ? 1 : 0,
                'transaction_id' => $apiResponse[0]->getId()
            ]);
            $result = $this->addReservationPayment([
                'reservation_ids'=> $data['reservationIds'],
                'payment_mode' => 40,
                'payment_status' => $paymentStatus,
                'transaction_id' => $apiResponse[0]->getId(),
                'amount' => $data['totalAmount'],
                'currency' => $data['currency'],
                'method' => 'add_reservation_payment',
                'language' => 'EN',
            ]);
            $result['statusCode'] = $apiResponse[1];
            $result['paymentStatus'] = $paymentStatus;
            return $result;
        } catch (Cybersource\ApiException $e) {
            return [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function capturePayment($data){

        $clientReferenceInformationArr = [
            "code" => "TC50171_3"
        ];
        $clientReferenceInformation = new Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

        $orderInformationAmountDetailsArr = [
                "totalAmount" => "102.21",
                "currency" => "USD"
        ];
        $orderInformationAmountDetails = new Ptsv2paymentsidcapturesOrderInformationAmountDetails($orderInformationAmountDetailsArr);

        $orderInformationArr = [
            "amountDetails" => $orderInformationAmountDetails
        ];
        $orderInformation = new Ptsv2paymentsidcapturesOrderInformation($orderInformationArr);

        $requestObjArr = [
                "clientReferenceInformation" => $clientReferenceInformation,
                "orderInformation" => $orderInformation
        ];
        $requestObj = new CapturePaymentRequest($requestObjArr);


        $commonElement = new ExternalConfiguration();
        $config = $commonElement->ConnectionHost();
        $merchantConfig = $commonElement->merchantConfigObject();

        $api_client = new ApiClient($config, $merchantConfig);
        $api_instance = new CaptureApi($api_client);

        try {
            $apiResponse = $api_instance->capturePayment($requestObj, $id);
            print_r(PHP_EOL);
            print_r($apiResponse);

            WriteLogAudit($apiResponse[1]);
            return $apiResponse;
        } catch (Cybersource\ApiException $e) {
            return [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getReservationData($reservationId)
    {
        $url = "https://www.planyo.com/rest/?method=get_reservation_data&api_key={$this->api_key}&reservation_id={$reservationId}&language=EN";
        $headers = $this->getHeaders();
        return $this->sendApiRequest($url, $headers);
    }

    public function addReservationPayment($data)
    {
        $reservation_ids = implode(',', $data['reservation_ids']);
        $amounts = implode(',', $data['amount']);
        $url = "https://www.planyo.com/rest/?reservation_ids={$reservation_ids}&payment_mode={$data['payment_mode']}&payment_status={$data['payment_status']}&transaction_id={$data['transaction_id']}&amounts={$amounts}&currency={$data['currency']}&language={$data['language']}&method={$data['method']}&api_key={$this->api_key}";
        $headers = $this->getHeaders();
        return $this->sendApiRequest($url, $headers);
    }

    public function getCartItems($cartId) {
        $url = "https://www.planyo.com/rest/?shopping_cart_id={$cartId}&detail_level=&page=&language=EN&api_key={$this->api_key}&method=get_cart_items";
        $headers = $this->getHeaders();
        return $this->sendApiRequest($url, $headers);
    }

    private function getHeaders()
    {
        return [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    private function getPaymentStatus($status)
    {
        switch ($status) {
            case 'AUTHORIZED':
            case 'PARTIAL_AUTHORIZED':
            case 'AUTHORIZED_PENDING_REVIEW':
            case 'PENDING_AUTHENTICATION':
            case 'PENDING_REVIEW':
                return 2;
            case 'AUTHORIZED_RISK_DECLINED':
            case 'DECLINED':
            case 'INVALID_REQUEST':
                return 3;
        }
    }
}
?>
