<?php

namespace App;

class Message
{
    public string $type;
    public array $data;
    
    public function __construct(string $type, array $data) 
    {
        $this->type = $type;
        $this->data = $data;
    }
    
    public function __toString()
    {
        return json_encode([
            'message_type' => $this->type,
            'message_data' => $this->data,
        ]);
    }
}
