<?php

include_once("Config.php");

class Encrypter {
    private $method ="AES-256-ECB";
    private $key = "a8a708c9d4c4efc3e4f8321f445b2a2f3c57de53f29ab09a3fddf3627d0c7d8a";

    public function Encriptar($data) {
        $encrypted = openssl_encrypt($data, $this->method , $this->key, 0);
        if($encrypted === false) {
            return $data;
            throw new Exception("Error al Encriptar los datos");
        }
        return base64_encode($encrypted);
    }

    public function Desencriptar($data) {
        $decoded = base64_decode($data);
        $decrypted = openssl_decrypt($decoded, $this->method, $this->key, 0);
        if($decrypted === false) {
            return $data;
            throw new Exception("Error al desencriptar los datos");
        }
        return $decrypted;
    } 

    public function DesencriptarArray($data, $types) {
        if (!is_array($data)) {
            throw new InvalidArgumentException("El parámetro debe ser un array");
        }
        foreach ($types as $key) {
            // Verifica si la clave existe en el array original
            if (array_key_exists($key, $data)) {
                // Desencripta el valor si existe y lo reasigna al array
                $data[$key] = $this->Desencriptar($data[$key]);
            }
        }
        return $data;
    }

    public function DesencriptarArrayDeArray($data, $types) {
        if (!is_array($data) || (count($data) > 0 && !is_array(reset($data)))) {
            throw new InvalidArgumentException("El parámetro debe ser un array de arrays");
        }
        foreach ($data as $index => $item) {
            foreach($types as $key) {
                if (isset($item[$key])) {
                    $data[$index][$key] = $this->Desencriptar($item[$key]);
                }
            }
        }
        return $data;
    }

    public function EncriptarArray($data, $types) {
        foreach ($types as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $this->Encriptar($data[$key]);
            }
        }
        return $data;
    }

    public function EncriptarArrayDeArray($data, $types) {
        if (!is_array($data) || (count($data) > 0 && !is_array(reset($data)))) {
            throw new InvalidArgumentException("El parámetro debe ser un array de arrays");
        }
        foreach ($data as $index => $item) {
            foreach($types as $key) {
                if (isset($item[$key])) {
                    $data[$index][$key] = $this->Encriptar($item[$key]);
                }
            }
        }
        return $data;
    }

}
