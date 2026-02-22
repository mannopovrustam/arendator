<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HikvisionController extends Controller
{
    public function getLicensePlates()
    {
        $cameraIp = '83.222.7.192';
        $port = '81';
        $username = 'admin';
        $password = 'Orzu1316';
        $endpoint = "http://{$cameraIp}:{$port}/ISAPI/Traffic/channels/100/vehicleDetect/plates";
        $searchEndpoint = "http://{$cameraIp}:{$port}/ISAPI/ContentMgmt/search";

        $xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
        <AfterTime version="2.0" xmlns="http://www.hikvision.com/ver20/XMLSchema">
            <picTime>2025-06-23T00:00:00Z</picTime>
        </AfterTime>';

        $client = new Client([
            'auth' => [$username, $password, 'digest'],
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ]);

        try {
            $response = $client->post($endpoint, ['body' => $xmlBody]);
            $xml = simplexml_load_string($response->getBody()->getContents());
            dd($xml);
            $plates = [];

            foreach ($xml->Plate as $plate) {
                $captureTime = (string) $plate->captureTime;
                $plateNumber = (string) $plate->plateNumber;
                $picName = (string) $plate->picName;
                $imageData = null;

                // Способ 1: Проверка picURL
                $picUrl = (string) ($plate->picURL ?? '');
                if ($picUrl) {
                    try {
                        $imageResponse = $client->get($picUrl);
                        $imageContent = $imageResponse->getBody()->getContents();
                        $imagePath = "plates/{$picName}.jpg";
                        Storage::disk('public')->put($imagePath, $imageContent);
                        $imageData = Storage::url($imagePath);
                        Log::info("Изображение для picName {$picName} загружено по picURL: {$picUrl}");
                    } catch (RequestException $e) {
                        Log::error("Ошибка загрузки по picURL {$picUrl} для {$picName}: " . $e->getMessage());
                    }
                } else {
                    Log::warning("picURL отсутствует для picName {$picName}");
                }

                // Способ 2: Поиск через ISAPI ContentMgmt/search


                // http://83.222.7.192:81/ISAPI/Streaming/tracks/103/?starttime=20250623T120648Z&endtime=20250623T120648Z&name=ch01_0000000019700000176_01%4020250623170648_60831XAA&size=9414&token=08eb6a9c29b8a1749e4e384795557b36
                // http://83.222.7.192:81/ISAPI/Streaming/tracks/103/?starttime=20250623T120648Z&endtime=20250623T120648Z&name=ch01_0000000019700000176_02%4020250623170648_60831XAA&size=86970&token=b17cda964e5d789c8b30663ccc8df690


                // http://83.222.7.192:82/ISAPI/Streaming/tracks/103/?starttime=20250623T001242Z&endtime=20250623T001242Z&name=ch01_0000000019700000094_01%4020250623051242_60910NAA&size=14975&token=2dafdaa38c135f9b4e80433c99562c53
                if (!$imageData) {
                    $searchXml = '<?xml version="1.0" encoding="UTF-8"?>
                    <CMSearchDescription version="2.0" xmlns="http://www.hikvision.com/ver20/XMLSchema">
                        <searchID>' . uniqid() . '</searchID>
                        <trackIDList><trackID>101</trackID></trackIDList>
                        <timeSpanList>
                            <timeSpan>
                                <startTime>' . date('c', strtotime($captureTime . ' -1 minute')) . '</startTime>
                                <endTime>' . date('c', strtotime($captureTime . ' +1 minute')) . '</endTime>
                            </timeSpan>
                        </timeSpanList>
                        <contentTypeList><contentType>jpegpic</contentType></contentTypeList>
                        <maxResults>10</maxResults>
                        <searchResultPostion>0</searchResultPostion>
                    </CMSearchDescription>';

                    try {
                        $searchResponse = $client->post($searchEndpoint, ['body' => $searchXml]);
                        $searchXml = simplexml_load_string($searchResponse->getBody()->getContents());
                        Log::debug('Ответ поиска для picName ' . $picName . ': ' . $searchResponse->getBody()->getContents());
                        $imageUrl = (string) ($searchXml->searchResult->mediaSegmentDescriptor->playbackURI ?? null);

                        if ($imageUrl) {
                            $imageResponse = $client->get($imageUrl);
                            $imageContent = $imageResponse->getBody()->getContents();
                            $imagePath = "plates/{$picName}.jpg";
                            Storage::disk('public')->put($imagePath, $imageContent);
                            $imageData = Storage::url($imagePath);
                            Log::info("Изображение для picName {$picName} загружено через поиск: {$imageUrl}");
                        } else {
                            Log::warning("Изображение для picName {$picName} не найдено в поисковом запросе");
                        }
                    } catch (RequestException $e) {
                        Log::error("Ошибка поиска изображения {$picName}: " . $e->getMessage());
                    }
                }

                // Способ 3: Сконструированный URL
                if (!$imageData) {
                    $date = substr($captureTime, 0, 8); // YYYYMMDD
                    $imageUrl = "http://{$cameraIp}:{$port}/SDCard/{$date}/{$picName}.jpg";
                    try {
                        $imageResponse = $client->get($imageUrl);
                        $imageContent = $imageResponse->getBody()->getContents();
                        $imagePath = "plates/{$picName}.jpg";
                        Storage::disk('public')->put($imagePath, $imageContent);
                        $imageData = Storage::url($imagePath);
                        Log::info("Изображение для picName {$picName} загружено по URL: {$imageUrl}");
                    } catch (RequestException $e) {
                        Log::error("Ошибка загрузки изображения {$picName} по URL {$imageUrl}: " . $e->getMessage());
                    }
                }

                // Способ 4: FTP (если настроен)
                if (!$imageData && env('FTP_HOST')) {
                    $ftpAdapter = new FtpAdapter([
                        'host' => env('FTP_HOST'),
                        'username' => env('FTP_USERNAME'),
                        'password' => env('FTP_PASSWORD'),
                        'port' => 21,
                        'root' => '/ANPR/' . $date . '/',
                    ]);
                    $filesystem = new Filesystem($ftpAdapter);
                    try {
                        $imageContent = $filesystem->read("{$picName}.jpg");
                        $imagePath = "plates/{$picName}.jpg";
                        Storage::disk('public')->put($imagePath, $imageContent);
                        $imageData = Storage::url($imagePath);
                        Log::info("Изображение для picName {$picName} загружено с FTP");
                    } catch (\Exception $e) {
                        Log::error("Ошибка загрузки изображения {$picName} с FTP: " . $e->getMessage());
                    }
                }

                // Способ 5: Снимок как запасной вариант
                if (!$imageData) {
                    $snapshotUrl = "http://{$cameraIp}:{$port}/ISAPI/Streaming/channels/101/picture";
                    try {
                        $imageResponse = $client->get($snapshotUrl);
                        $imageContent = $imageResponse->getBody()->getContents();
                        $imagePath = "plates/{$picName}_snapshot.jpg";
                        Storage::disk('public')->put($imagePath, $imageContent);
                        $imageData = Storage::url($imagePath);
                        Log::info("Снимок для picName {$picName} загружен: {$snapshotUrl}");
                    } catch (RequestException $e) {
                        Log::error("Ошибка получения снимка для {$picName}: " . $e->getMessage());
                    }
                }

                $plates[] = [
                    'license_plate' => $plateNumber,
                    'camera' => 'Camera 1', // Обновите, если имя камеры доступно
                    'captured_time' => $captureTime,
                    'image' => $imageData,
                ];
            }

            Log::info('Успешно получены данные: ' . json_encode($plates));
            return response()->json(['status' => 'success', 'data' => $plates]);

        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            if ($e->hasResponse()) {
                $errorMessage .= ': ' . $e->getResponse()->getBody()->getContents();
            }
            Log::error("Ошибка запроса к Hikvision: {$errorMessage}");
            return response()->json([
                'status' => 'error',
                'message' => "Не удалось получить данные: {$errorMessage}",
            ], 500);
        }

    }
}
