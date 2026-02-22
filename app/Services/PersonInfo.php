<?php

namespace App\Services;

use Illuminate\Http\Request;

class PersonInfo
{
    public function postPassportData(Request $request)
    {

        $myinfo = [
            "Pinpp" => "33110950530015",
            "surname" => "MANNOPOV",
            "firstname" => "RUSTAM",
            "lastname" => "RAVSHAN O‘G‘LI",
            "datebirth" => "31.10.1995",
            "sex" => "M",
            "datePassport" => "10.04.2025",
            "IssuedBy" => "МИГРАЦИЯ ВА ФУҚАРОЛИКНИ РАСМИЙЛАШТИРИШ БОШ БОШҚАРМАСИ",
            "PassportIssuedBy" => "МИГРАЦИЯ ВА ФУҚАРОЛИКНИ РАСМИЙЛАШТИРИШ БОШ БОШҚАРМАСИ",
            "Pcitizen" => "5396208441",
            "BirthPlace" => "TOSHKENT TUMANI",
            "message" => "ХЧКваФРБ тизимидан олинган маълумотлар"
        ];
        return ['psp' => $myinfo];

        $request->validate([
            'passport' => 'required|string',
//            'citizen' => 'required|integer',
            'birthday' => 'required|date',
            'ch_info' => 'required',
        ]);

        $hash = md5('silkroad_emehmon' . date('YmdH') . 'pspdtb');
        if ($hash != $request->input('ch_info')) return false;

        $data['psp'] = $request->passport;
        $data['dtb'] = $request->birthday;
        $data['country'] = 173;
        $response = $this->reachDataFromMVD($data);

        \Log::info($response);
        if (isset($response['psp']) && is_string($response['psp'])) {
            $psp = json_decode($response['psp'], true);
            if (isset($psp['name'])) {
                $nameParts = explode(' ', $psp['name']);
                $response['psp'] = [
                    'surname' => strtoupper($nameParts[0] ?? ''),
                    'firstname' => strtoupper($nameParts[1] ?? ''),
                    'lastname' => isset($nameParts[2]) ? mb_convert_case(implode(' ', array_slice($nameParts, 2)), MB_CASE_TITLE, 'UTF-8') : 'XXX',
                    'sex' => isset($psp['sex']) ? ($psp['sex'] == 1 ? 'M' : 'F') : null,
                ];
            }
        }

        if (isset($response['status'], $response['psp']) && $response['status'] === 'success' && (isset($response['psp']['surname']))) {
            return response()->json(['psp' => $response['psp']]);
        } else {
            return response()->json(['message' => 'Passport not found or error occurred.'], 404);
        }
    }


    protected function curlRequest($url, $jsonData)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    protected function reachDataFromMVD($data)
    {
        try {
            if (!$data) return null;
            if ($data['country'] != 173) {
                $data['hash'] = md5('CheckAIDS2022' . $data['psp'] . $data['dtb'] . date('y-m-d'));
                $url = 'https://emehmon.uz/sadhgfksdaj-876jhgsa-chet';
            } else {
                $data['hash'] = md5('CheckAIDS2022' . $data['psp'] . $data['dtb'] . date('y-m-d'));
                $url = 'https://emehmon.uz/sadhgfksdaj-876jhgsa';
            }
            $d = json_encode($data, JSON_UNESCAPED_UNICODE);

            $response = $this->curlRequest($url, $d);
            $arr = json_decode($response, true);
            if (!$arr || !isset($arr['status'])) return null;
            return $arr;
        } catch (\Exception $ex) {
            \Log::error('MVD: ' . $data['psp'] . ' -> ' . $ex->getMessage());
            return null;
        }
    }

}
