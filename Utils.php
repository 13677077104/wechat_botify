<?php

class Utils
{
    private $baseUrl;

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function get($uri, array $query = null): array
    {
        $baseUrl = $this->getBaseUrl();
        if (!$baseUrl) {
            return ['error' => 'base_url 未设置，使用setBaseUrl() 进行设置'];
        }
        $url = sprintf("%s/%s", $baseUrl, ltrim($uri, '/'));
        if ($query) {
            $url = sprintf("%s?%s", $url, http_build_query($query));
        }
        echo $url . PHP_EOL;
        $response = $this->sendRequest($url, 'GET');
        return json_decode($response, true);
    }

    public function post($uri, $data, array $query = null): array
    {
        $baseUrl = $this->getBaseUrl();
        if (!$baseUrl) {
            return ['error' => 'base_url 未设置，使用setBaseUrl() 进行设置'];
        }
        $url = sprintf("%s/%s", $baseUrl, ltrim($uri, '/'));
        if ($query) {
            $url = sprintf("%s?%s", $url, http_build_query($query));
        }
        $response = $this->sendRequest($url, 'POST', $data);
        return json_decode($response, true);
    }

    private function sendRequest($url, $method, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data)),
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}