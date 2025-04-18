<?php  

namespace App\Http\Service;

use App\Http\Models\Promo;

class PromoService {
    
    public function create(array $data) {
        return Promo::create([   
            'name' => $data['name'],
            'discount' => $data['discount'],
            'start_date_flash_sale' => $data['start_date_flash_sale'],
            'end_date_flash_sale' => $data['end_date_flash_sale'],
        ]);
    }

    public function update($uuid, array $data) {
        $promo_model = new Promo();
        $promo = $promo_model->getPromoByUuid($uuid);
        
        $promo->update([   
            'name' => $data['name'],
            'discount' => $data['discount'],
            'start_date_flash_sale' => $data['start_date_flash_sale'],
            'end_date_flash_sale' => $data['end_date_flash_sale'],
        ]);

        return $promo->fresh();
    }

    public function delete($uuid){
        $promo_model = new Promo();
        $promo = $promo_model->getPromoByUuid($uuid);

        $promo->delete();
    }
}
