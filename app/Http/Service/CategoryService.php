<?php  

namespace App\Http\Service;

use App\Http\Models\Category;

class CategoryService {

    public function create(array $data) {
        return Category::create([   
            'title' => $data['title'],
        ]);
    }

    public function update($uuid, array $data) {
        $category_model = new Category();
        $category = $category_model->getCategoryByUuid($uuid);
        
        $category->update([   
            'title' => $data['title'],
        ]);

        return $category->fresh();
    }

    public function delete($uuid){
        $category_model = new Category();
        $category = $category_model->getCategoryByUuid($uuid);

        $category->delete();
    }
}
