<?php  

namespace App\Http\Service;

use App\Http\Models\Book;

class BookService {
    public function create(array $data) {

        $book = Book::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'image' => $data['image'],
            'variant_code' => $data['variant_code'],
            'variant_name' => $data['variant_name'],
            'date_publish' => $data['date_publish'],
            'page' => $data['page'],
            'ISBN' => $data['ISBN'],
            'language' => $data['language'],
            'long' => $data['long'],
            'weight' => $data['weight'],
            'width' => $data['width'],
            'uuid_sub_category' => $data['uuid_sub_category'],
            'uuid_writer' => $data['uuid_writer'],
            'uuid_publisher' => $data['uuid_publisher'],
        ]);

        return $book->load('stores');
        
    }

    public function update($uuid, array $data) {
        
        $book_model = new Book();
        $book = $book_model->getBookByUuid($uuid);
        
        $book->update([   
            'name' => $data['name'],
            'description' => $data['description'],
            'image' => $data['image'],
            'variant_code' => $data['variant_code'],
            'variant_name' => $data['variant_name'],
            'date_publish' => $data['date_publish'],
            'page' => $data['page'],
            'ISBN' => $data['ISBN'],
            'language' => $data['language'],
            'long' => $data['long'],
            'weight' => $data['weight'],
            'width' => $data['width'],
            'uuid_sub_category' => $data['uuid_sub_category'],
            'uuid_writer' => $data['uuid_writer'],
            'uuid_publisher' => $data['uuid_publisher'],
        ]);

        return $book->fresh();
    }

    public function delete($uuid){
        $book_model = new Book();
        $book = $book_model->getBookByUuid($uuid);

        $book->delete();
    }
}
