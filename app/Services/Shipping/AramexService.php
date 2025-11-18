<?php

namespace App\Services\Shipping;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexService
{
    private $baseUrl;
    private $clientInfo;

    public function __construct($config)
    {
        // Use sandbox or production URL based on config
        $this->baseUrl = isset($config['is_production']) && $config['is_production'] 
            ? 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json'
            : 'https://ws.sbx.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json';

        $this->clientInfo = [
            'UserName' => $config['username'] ?? '',
            'Password' => $config['password'] ?? '',
            'Version' => 'v1',
            'AccountNumber' => $config['account_number'] ?? '',
            'AccountPin' => $config['account_pin'] ?? '',
            'AccountEntity' => $config['account_entity'] ?? '',
            'AccountCountryCode' => $config['account_country_code'] ?? '',
            'Source' => 24
        ];
    }

    /**
     * Test the connection to Aramex API
     */
    public function testConnection(): array
    {
        try {
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'Entity' => $this->clientInfo['AccountEntity'],
                'ProductGroup' => 'DOM',
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($this->baseUrl . '/GetLastShipmentsNumbersRange', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if there are any errors in the response
                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'API Error';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    
                    return [
                        'is_configured' => false,
                        'message' => 'Aramex API Error: ' . $errorMessage
                    ];
                }

                return [
                    'is_configured' => true,
                    'message' => 'Connection successful! Aramex API is working.'
                ];
            }

            return [
                'is_configured' => false,
                'message' => 'Failed to connect to Aramex API. HTTP Status: ' . $response->status()
            ];

        } catch (Exception $e) {
            Log::error('Aramex connection test failed', [
                'error' => $e->getMessage(),
                'config' => $this->maskSensitiveData($this->clientInfo)
            ]);

            return [
                'is_configured' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate shipping rates using Aramex Rate Calculator API
     */
    public function calculateRates($origin, $destination, $packages): array
    {
        try {
            $totalWeight = collect($packages)->sum('weight');
            
            // Use Aramex Rate Calculator API
            $rateCalculatorUrl = str_replace(
                '/ShippingAPI.V2/Shipping/Service_1_0.svc/json',
                '/ShippingAPI.V2/RateCalculator/Service_1_0.svc/json/CalculateRate',
                $this->baseUrl
            );
            
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'DestinationAddress' => [
                    'Line1' => $destination['line1'] ?? '',
                    'Line2' => $destination['line2'] ?? '',
                    'Line3' => '',
                    'City' => $destination['city'] ?? '',
                    'StateOrProvinceCode' => '',
                    'PostCode' => $destination['postal_code'] ?? '',
                    'CountryCode' => $destination['country'] ?? '',
                    'Longitude' => 0,
                    'Latitude' => 0,
                    'BuildingNumber' => null,
                    'BuildingName' => null,
                    'Floor' => null,
                    'Apartment' => null,
                    'POBox' => null,
                    'Description' => null
                ],
                'OriginAddress' => [
                    'Line1' => $origin['line1'] ?? '',
                    'Line2' => $origin['line2'] ?? '',
                    'Line3' => '',
                    'City' => $origin['city'] ?? '',
                    'StateOrProvinceCode' => '',
                    'PostCode' => $origin['postal_code'] ?? '',
                    'CountryCode' => $origin['country'] ?? '',
                    'Longitude' => 0,
                    'Latitude' => 0,
                    'BuildingNumber' => null,
                    'BuildingName' => null,
                    'Floor' => null,
                    'Apartment' => null,
                    'POBox' => null,
                    'Description' => null
                ],
                'PreferredCurrencyCode' => 'EGP',
                'ShipmentDetails' => [
                    'Dimensions' => null,
                    'ActualWeight' => [
                        'Unit' => 'KG',
                        'Value' => $totalWeight
                    ],
                    'ChargeableWeight' => null,
                    'DescriptionOfGoods' => 'General Goods',
                    'GoodsOriginCountry' => $origin['country'] ?? '',
                    'NumberOfPieces' => count($packages),
                    'ProductGroup' => $origin['country'] === $destination['country'] ? 'DOM' : 'EXP',
                    'ProductType' => $origin['country'] === $destination['country'] ? 'CDS' : 'PDX',
                    'PaymentType' => 'P',
                    'PaymentOptions' => '',
                    'CustomsValueAmount' => null,
                    'CashOnDeliveryAmount' => null,
                    'InsuranceAmount' => null,
                    'CashAdditionalAmount' => null,
                    'CashAdditionalAmountDescription' => null,
                    'CollectAmount' => null,
                    'Services' => '',
                    'Items' => null,
                    'DeliveryInstructions' => null
                ],
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($rateCalculatorUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Rate calculation failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                // Parse the rate response
                $rates = [];
                if (isset($data['TotalAmount']['Value']) && $data['TotalAmount']['Value'] > 0) {
                    $productGroup = $payload['ShipmentDetails']['ProductGroup'];
                    $serviceName = $productGroup === 'DOM' ? 'Aramex Domestic' : 'Aramex International Express';
                    
                    $rates[] = [
                        'service_name' => $serviceName,
                        'service_code' => $productGroup,
                        'price' => $data['TotalAmount']['Value'],
                        'currency' => $data['TotalAmount']['CurrencyCode'] ?? 'USD',
                        'estimated_days' => $productGroup === 'DOM' ? 2 : 5,
                        'carrier_name' => 'Aramex'
                    ];
                }

                return $rates;
            }

            throw new Exception('Failed to calculate rates. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex rate calculation failed', [
                'error' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination
            ]);

            // Fallback to estimated rates
            return $this->getFallbackRates($origin, $destination, $packages);
        }
    }

    /**
     * Fallback rates when API fails
     */
    private function getFallbackRates($origin, $destination, $packages): array
    {
        $totalWeight = collect($packages)->sum('weight');
        $rates = [];

        // Domestic rates (same country) - Egyptian market rates
        if ($origin['country'] === $destination['country']) {
            $rates[] = [
                'service_name' => 'Aramex Domestic (Estimated)',
                'service_code' => 'DOM',
                'price' => $this->calculateEgyptianDomesticRate($totalWeight, $origin, $destination),
                'currency' => 'EGP',
                'estimated_days' => 2,
                'carrier_name' => 'Aramex'
            ];
        } else {
            // International rates - still higher for international
            $rates[] = [
                'service_name' => 'Aramex International Express (Estimated)',
                'service_code' => 'INT',
                'price' => $this->calculateEgyptianInternationalRate($totalWeight),
                'currency' => 'EGP',
                'estimated_days' => 5,
                'carrier_name' => 'Aramex'
            ];
        }

        return $rates;
    }

    /**
     * Create a shipment
     */
    public function createShipment($shipmentData): array
    {
        try {
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'LabelInfo' => null,
                'Shipments' => [
                    [
                        'Reference1' => $shipmentData['reference'] ?? '',
                        'Reference2' => '',
                        'Reference3' => '',
                        'Shipper' => [
                            'Reference1' => '',
                            'Reference2' => '',
                            'AccountNumber' => $this->clientInfo['AccountNumber'],
                            'PartyAddress' => [
                                'Line1' => $shipmentData['origin']['line1'] ?? '',
                                'Line2' => $shipmentData['origin']['line2'] ?? '',
                                'Line3' => '',
                                'City' => $shipmentData['origin']['city'] ?? '',
                                'StateOrProvinceCode' => '',
                                'PostCode' => $shipmentData['origin']['postal_code'] ?? '',
                                'CountryCode' => $shipmentData['origin']['country'] ?? '',
                                'Longitude' => 0,
                                'Latitude' => 0,
                                'BuildingNumber' => null,
                                'BuildingName' => null,
                                'Floor' => null,
                                'Apartment' => null,
                                'POBox' => null,
                                'Description' => null
                            ],
                            'Contact' => [
                                'Department' => '',
                                'PersonName' => $shipmentData['shipper_name'] ?? 'DNP Store',
                                'Title' => '',
                                'CompanyName' => $shipmentData['shipper_company'] ?? 'DNP Store',
                                'PhoneNumber1' => $shipmentData['shipper_phone'] ?? '',
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => $shipmentData['shipper_phone'] ?? '',
                                'EmailAddress' => $shipmentData['shipper_email'] ?? '',
                                'Type' => ''
                            ]
                        ],
                        'Consignee' => [
                            'Reference1' => '',
                            'Reference2' => '',
                            'AccountNumber' => '',
                            'PartyAddress' => [
                                'Line1' => $shipmentData['destination']['line1'] ?? '',
                                'Line2' => $shipmentData['destination']['line2'] ?? '',
                                'Line3' => '',
                                'City' => $shipmentData['destination']['city'] ?? '',
                                'StateOrProvinceCode' => '',
                                'PostCode' => $shipmentData['destination']['postal_code'] ?? '',
                                'CountryCode' => $shipmentData['destination']['country'] ?? '',
                                'Longitude' => 0,
                                'Latitude' => 0,
                                'BuildingNumber' => '',
                                'BuildingName' => '',
                                'Floor' => '',
                                'Apartment' => '',
                                'POBox' => null,
                                'Description' => ''
                            ],
                            'Contact' => [
                                'Department' => '',
                                'PersonName' => $shipmentData['consignee_name'] ?? '',
                                'Title' => '',
                                'CompanyName' => $shipmentData['consignee_company'] ?? '',
                                'PhoneNumber1' => $shipmentData['consignee_phone'] ?? '',
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => $shipmentData['consignee_phone'] ?? '',
                                'EmailAddress' => $shipmentData['consignee_email'] ?? '',
                                'Type' => ''
                            ]
                        ],
                        'ThirdParty' => [
                            'Reference1' => '',
                            'Reference2' => '',
                            'AccountNumber' => '',
                            'PartyAddress' => [
                                'Line1' => '',
                                'Line2' => '',
                                'Line3' => '',
                                'City' => '',
                                'StateOrProvinceCode' => '',
                                'PostCode' => '',
                                'CountryCode' => '',
                                'Longitude' => 0,
                                'Latitude' => 0,
                                'BuildingNumber' => null,
                                'BuildingName' => null,
                                'Floor' => null,
                                'Apartment' => null,
                                'POBox' => null,
                                'Description' => null
                            ],
                            'Contact' => [
                                'Department' => '',
                                'PersonName' => '',
                                'Title' => '',
                                'CompanyName' => '',
                                'PhoneNumber1' => '',
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => '',
                                'EmailAddress' => '',
                                'Type' => ''
                            ]
                        ],
                        'ShippingDateTime' => '/Date(' . (time() * 1000) . '+0000)/',
                        'DueDate' => '/Date(' . ((time() + 86400) * 1000) . '+0000)/', // +1 day
                        'Comments' => $shipmentData['comments'] ?? '',
                        'PickupLocation' => '',
                        'OperationsInstructions' => '',
                        'AccountingInstrcutions' => '',
                        'Details' => [
                            'Dimensions' => null,
                            'ActualWeight' => [
                                'Unit' => 'KG',
                                'Value' => $shipmentData['weight'] ?? 0.5
                            ],
                            'ChargeableWeight' => null,
                            'DescriptionOfGoods' => $shipmentData['description'] ?? 'General Goods',
                            'GoodsOriginCountry' => $shipmentData['origin']['country'] ?? '',
                            'NumberOfPieces' => count($shipmentData['packages'] ?? [1]),
                            'ProductGroup' => $shipmentData['product_group'] ?? 'EXP',
                            'ProductType' => $shipmentData['product_type'] ?? 'PDX',
                            'PaymentType' => 'P',
                            'PaymentOptions' => '',
                            'CustomsValueAmount' => null,
                            'CashOnDeliveryAmount' => null,
                            'InsuranceAmount' => null,
                            'CashAdditionalAmount' => null,
                            'CashAdditionalAmountDescription' => '',
                            'CollectAmount' => null,
                            'Services' => '',
                            'Items' => []
                        ],
                        'Attachments' => [],
                        'ForeignHAWB' => '',
                        'TransportType ' => 0,
                        'PickupGUID' => '',
                        'Number' => null,
                        'ScheduledDelivery' => null
                    ]
                ],
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(60)->post($this->baseUrl . '/CreateShipments', $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Shipment creation failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }

                    Log::error('Aramex API returned errors', [
                        'notifications' => $data['Notifications'] ?? [],
                        'full_response' => $data
                    ]);

                    throw new Exception($errorMessage);
                }

                // Extract tracking number from response
                $trackingNumber = null;
                if (isset($data['Shipments'][0]['ID'])) {
                    $trackingNumber = $data['Shipments'][0]['ID'];
                }

                return [
                    'tracking_number' => $trackingNumber,
                    'carrier_response' => $data,
                    'success' => true
                ];
            }

            throw new Exception('Failed to create shipment. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex shipment creation failed', [
                'error' => $e->getMessage(),
                'shipmentData' => $this->maskSensitiveData($shipmentData)
            ]);

            throw $e;
        }
    }

    private function calculateDomesticRate($weight): float
    {
        // Basic domestic rate calculation
        $baseRate = 15.00;
        $perKgRate = 3.00;
        
        return $baseRate + ($weight * $perKgRate);
    }

    private function calculateInternationalRate($weight, $origin, $destination): float
    {
        // Basic international rate calculation
        $baseRate = 35.00;
        $perKgRate = 8.00;
        
        return $baseRate + ($weight * $perKgRate);
    }

    private function maskSensitiveData($data): array
    {
        $masked = $data;
        $sensitiveKeys = ['password', 'Password', 'AccountPin', 'account_pin'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($masked[$key])) {
                $masked[$key] = '***masked***';
            }
        }
        
        return $masked;
    }

    private function calculateEgyptianDomesticRate($weight, $origin = null, $destination = null)
    {
        // Egyptian market rates in EGP with city-based variations
        $baseRate = 40.00;
        $perKgRate = 10.00;
        
        // City distance multipliers for realistic Egyptian shipping
        $distanceMultiplier = $this->getEgyptianCityDistanceMultiplier($origin, $destination);
        
        $totalRate = ($baseRate + ($weight * $perKgRate)) * $distanceMultiplier;
        
        return round($totalRate, 2);
    }

    private function getEgyptianCityDistanceMultiplier($origin, $destination)
    {
        if (!$origin || !$destination) {
            return 1.0; // Default multiplier
        }

        $originCity = $origin['city'] ?? '';
        $destinationCity = $destination['city'] ?? '';

        // Define city zones based on distance from major hubs
        $nearCities = ['Cairo', 'Giza', 'Alexandria', 'Qalyubia', 'Banha'];
        $mediumCities = ['Mansoura', 'Tanta', 'Zagazig', 'Damietta', 'Ismailia', 'Suez', 'Port Said'];
        $farCities = ['Aswan', 'Luxor', 'Sohag', 'Qena', 'Asyut', 'Minya', 'Hurghada', 'Arish'];

        // Rate multipliers based on destination distance
        if (in_array($destinationCity, $nearCities)) {
            return 1.0; // Base rate
        } elseif (in_array($destinationCity, $mediumCities)) {
            return 1.3; // 30% higher
        } elseif (in_array($destinationCity, $farCities)) {
            return 1.6; // 60% higher for far cities
        }

        return 1.2; // Default 20% higher for unlisted cities
    }

    private function calculateEgyptianInternationalRate($weight)
    {
        // International from Egypt in EGP
        // Base rate: 200 EGP + 50 EGP per kg
        return 200.00 + ($weight * 50.00);
    }

    /**
     * Print shipping label
     */
    public function printLabel($shipmentNumber, $originEntity = 'CAI', $productGroup = 'DOM'): array
    {
        try {
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'LabelInfo' => [
                    'ReportID' => 9729,
                    'ReportType' => 'URL' // URL or PDF
                ],
                'OriginEntity' => $originEntity,
                'ProductGroup' => $productGroup,
                'ShipmentNumber' => $shipmentNumber,
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($this->baseUrl . '/PrintLabel', $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Label printing failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                return [
                    'success' => true,
                    'label_url' => $data['ShipmentLabel']['LabelURL'] ?? null,
                    'label_file_contents' => $data['ShipmentLabel']['LabelFileContents'] ?? null,
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to print label. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex label printing failed', [
                'error' => $e->getMessage(),
                'shipment_number' => $shipmentNumber
            ]);

            throw $e;
        }
    }

    /**
     * Track shipments by tracking numbers
     */
    public function trackShipments($shipmentNumbers, $getLastUpdateOnly = false): array
    {
        try {
            // Ensure shipmentNumbers is an array
            if (!is_array($shipmentNumbers)) {
                $shipmentNumbers = [$shipmentNumbers];
            }

            $trackingUrl = str_replace(
                '/ShippingAPI.V2/Shipping/Service_1_0.svc/json',
                '/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments',
                $this->baseUrl
            );

            $payload = [
                'ClientInfo' => $this->clientInfo,
                'GetLastTrackingUpdateOnly' => $getLastUpdateOnly,
                'Shipments' => $shipmentNumbers,
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($trackingUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Tracking failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                return [
                    'success' => true,
                    'tracking_results' => $data['TrackingResults'] ?? [],
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to track shipments. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex tracking failed', [
                'error' => $e->getMessage(),
                'shipment_numbers' => $shipmentNumbers
            ]);

            throw $e;
        }
    }

    /**
     * Create a pickup request
     */
    public function createPickup($pickupData): array
    {
        try {
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'LabelInfo' => [
                    'ReportID' => 9729,
                    'ReportType' => 'URL'
                ],
                'Pickup' => [
                    'PickupAddress' => [
                        'Line1' => $pickupData['address']['line1'] ?? '',
                        'Line2' => $pickupData['address']['line2'] ?? '',
                        'Line3' => '',
                        'City' => $pickupData['address']['city'] ?? '',
                        'StateOrProvinceCode' => '',
                        'PostCode' => $pickupData['address']['postal_code'] ?? '',
                        'CountryCode' => $pickupData['address']['country'] ?? '',
                        'Longitude' => 0,
                        'Latitude' => 0,
                        'BuildingNumber' => null,
                        'BuildingName' => null,
                        'Floor' => null,
                        'Apartment' => null,
                        'POBox' => null,
                        'Description' => null
                    ],
                    'PickupContact' => [
                        'Department' => '',
                        'PersonName' => $pickupData['contact']['person_name'] ?? '',
                        'Title' => '',
                        'CompanyName' => $pickupData['contact']['company_name'] ?? '',
                        'PhoneNumber1' => $pickupData['contact']['phone'] ?? '',
                        'PhoneNumber1Ext' => '',
                        'PhoneNumber2' => '',
                        'PhoneNumber2Ext' => '',
                        'FaxNumber' => '',
                        'CellPhone' => $pickupData['contact']['phone'] ?? '',
                        'EmailAddress' => $pickupData['contact']['email'] ?? '',
                        'Type' => ''
                    ],
                    'PickupLocation' => $pickupData['pickup_location'] ?? 'Reception',
                    'PickupDate' => $this->formatAramexDate($pickupData['pickup_date']),
                    'ReadyTime' => $this->formatAramexDate($pickupData['ready_time']),
                    'LastPickupTime' => $this->formatAramexDate($pickupData['last_pickup_time']),
                    'ClosingTime' => $this->formatAramexDate($pickupData['closing_time'] ?? $pickupData['last_pickup_time']),
                    'Comments' => $pickupData['comments'] ?? '',
                    'Reference1' => $pickupData['reference'] ?? '',
                    'Reference2' => '',
                    'Vehicle' => '',
                    'Shipments' => $pickupData['shipments'] ?? [],
                    'PickupItems' => $pickupData['pickup_items'] ?? [],
                    'Status' => 'Ready',
                    'ExistingShipments' => null,
                    'Branch' => '',
                    'RouteCode' => ''
                ],
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(60)->post($this->baseUrl . '/CreatePickup', $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Pickup creation failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                return [
                    'success' => true,
                    'pickup_guid' => $data['ProcessedPickup']['GUID'] ?? null,
                    'pickup_reference' => $data['ProcessedPickup']['Reference1'] ?? null,
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to create pickup. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex pickup creation failed', [
                'error' => $e->getMessage(),
                'pickupData' => $this->maskSensitiveData($pickupData)
            ]);

            throw $e;
        }
    }

    /**
     * Track pickup by reference or GUID
     */
    public function trackPickup($reference): array
    {
        try {
            $trackingUrl = str_replace(
                '/ShippingAPI.V2/Shipping/Service_1_0.svc/json',
                '/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackPickup',
                $this->baseUrl
            );

            $payload = [
                'ClientInfo' => $this->clientInfo,
                'Reference' => $reference,
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($trackingUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Pickup tracking failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                return [
                    'success' => true,
                    'pickup_info' => $data['PickupInfo'] ?? null,
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to track pickup. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex pickup tracking failed', [
                'error' => $e->getMessage(),
                'reference' => $reference
            ]);

            throw $e;
        }
    }

    /**
     * Cancel a scheduled pickup
     */
    public function cancelPickup($pickupGuid, $comments = ''): array
    {
        try {
            $payload = [
                'ClientInfo' => $this->clientInfo,
                'PickupGUID' => $pickupGuid,
                'Comments' => $comments,
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($this->baseUrl . '/CancelPickup', $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Pickup cancellation failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }
                    throw new Exception($errorMessage);
                }

                return [
                    'success' => true,
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to cancel pickup. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex pickup cancellation failed', [
                'error' => $e->getMessage(),
                'pickup_guid' => $pickupGuid
            ]);

            throw $e;
        }
    }

    /**
     * Validate shipping address
     */
    public function validateAddress($address): array
    {
        try {
            $locationUrl = str_replace(
                '/ShippingAPI.V2/Shipping/Service_1_0.svc/json',
                '/ShippingAPI.V2/Location/Service_1_0.svc/json/ValidateAddress',
                $this->baseUrl
            );

            $payload = [
                'ClientInfo' => $this->clientInfo,
                'Address' => [
                    'Line1' => $address['line1'] ?? '',
                    'Line2' => $address['line2'] ?? '',
                    'Line3' => '',
                    'City' => $address['city'] ?? '',
                    'StateOrProvinceCode' => $address['state'] ?? '',
                    'PostCode' => $address['postal_code'] ?? '',
                    'CountryCode' => $address['country'] ?? '',
                    'Longitude' => 0,
                    'Latitude' => 0,
                    'BuildingNumber' => null,
                    'BuildingName' => null,
                    'Floor' => null,
                    'Apartment' => null,
                    'POBox' => null,
                    'Description' => null
                ],
                'Transaction' => [
                    'Reference1' => '',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => ''
                ]
            ];

            $response = Http::timeout(30)->post($locationUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['HasErrors']) && $data['HasErrors'] === true) {
                    $errorMessage = 'Address validation failed';
                    if (isset($data['Notifications']) && is_array($data['Notifications'])) {
                        $errors = collect($data['Notifications'])->pluck('Message')->implode(', ');
                        $errorMessage = $errors ?: $errorMessage;
                    }

                    return [
                        'success' => false,
                        'is_valid' => false,
                        'error' => $errorMessage,
                        'carrier_response' => $data
                    ];
                }

                // Aramex returns validation status
                $isValid = isset($data['IsValid']) ? $data['IsValid'] : false;

                return [
                    'success' => true,
                    'is_valid' => $isValid,
                    'suggestions' => $data['SuggestedAddresses'] ?? [],
                    'carrier_response' => $data
                ];
            }

            throw new Exception('Failed to validate address. HTTP Status: ' . $response->status());

        } catch (Exception $e) {
            Log::error('Aramex address validation failed', [
                'error' => $e->getMessage(),
                'address' => $address
            ]);

            throw $e;
        }
    }

    /**
     * Format date/time for Aramex API
     * Aramex uses: /Date(1484096770000-0500)/
     */
    private function formatAramexDate($dateTime): string
    {
        if (is_string($dateTime)) {
            $timestamp = strtotime($dateTime);
        } elseif ($dateTime instanceof \DateTime) {
            $timestamp = $dateTime->getTimestamp();
        } else {
            $timestamp = $dateTime;
        }

        $milliseconds = $timestamp * 1000;
        return "/Date({$milliseconds}+0000)/";
    }
}