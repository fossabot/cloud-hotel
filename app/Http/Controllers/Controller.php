<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class Controller extends BaseController
{
    /***
     * @var \Illuminate\Http\Request
     */
    protected $request;
    protected $hotelId = 0;
    
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    public function removeImage($image)
    {
        $image = storage_path('app' . DIRECTORY_SEPARATOR . $image);
        
        if (file_exists($image)) {
            unlink($image);
        }
    }
    
    protected function _validate(array $rules, array $messages = [], array $customAttributes = [], callable $callback = null)
    {
        $validate = app('validator')->make(
            $this->request->all(), $rules, $messages, $customAttributes
        );
        
        if ($callback !== null && $callback instanceof \Closure) {
            $validate->after(function (Validator $validator) use (&$callback) {
                return $callback($validator);
            });
        }
        
        if($validate->fails()) {
            $this->throwValidationException($this->request, $validate);
        }
    }
}
