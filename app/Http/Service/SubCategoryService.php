<?php  

namespace App\Http\Service;

use App\Http\Models\SubCategory;

class SubCategoryService {

    public function create(array $data) {
        return SubCategory::create([   
            'title' => $data['title'],
            'uuid_category' => $data['uuid_category'],
            'uuid_parent_sub_category' => $data['uuid_parent_sub_category'],
        ]);
    }

    public function update($uuid, array $data) {
        $sub_category_model = new SubCategory();
        $sub_category = $sub_category_model->getSubCategoryByUuid($uuid);
        
        $sub_category->update([   
            'title' => $data['title'],
            'uuid_category' => $data['uuid_category'],
            'uuid_parent_sub_category' => $data['uuid_parent_sub_category'],
        ]);

        return $sub_category->fresh();
    }

    public function delete($uuid){
        $sub_category_model = new SubCategory();
        $sub_category = $sub_category_model->getSubCategoryByUuid($uuid);

        $sub_category->delete();
    }
}
