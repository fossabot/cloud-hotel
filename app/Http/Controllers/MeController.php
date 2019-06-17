<?php
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\UserResource;
    
    class MeController extends Controller
    {
        
        public function __invoke()
        {
            return _response(
              new UserResource($this->request->user())
            );
        }
    }
