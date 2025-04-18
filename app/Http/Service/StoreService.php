<?php  

namespace App\Http\Service;

use App\Http\Models\Store;

class StoreService {

    public function create(array $data) {
        return Store::create([   
            'name' => $data['name'],
            'address' => $data['address'],
            'city_name' => $data['city_name'],
        ]);
    }

    public function update($uuid, array $data) {
        $store_model = new Store();
        $store = $store_model->getStoreByUuid($uuid);
        
        $store->update([   
            'name' => $data['name'],
            'address' => $data['address'],
            'city_name' => $data['city_name'],
        ]);

        return $store->fresh();
    }

    public function delete($uuid){
        $store_model = new Store();
        $store = $store_model->getStoreByUuid($uuid);

        $store->delete();
    }
}
