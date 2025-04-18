<?php  

namespace App\Http\Service;

use App\Http\Models\Publisher;

class PublisherService {

    public function create(array $data) {
        return Publisher::create([   
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    public function update($uuid, array $data) {
        $publisher_model = new Publisher();
        $publisher = $publisher_model->getPublisherByUuid($uuid);
        
        $publisher->update([   
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return $publisher->fresh();
    }

    public function delete($uuid){
        $publisher_model = new Publisher();
        $publisher = $publisher_model->getPublisherByUuid($uuid);

        $publisher->delete();
    }
}
