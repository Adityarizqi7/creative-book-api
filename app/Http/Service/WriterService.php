<?php  

namespace App\Http\Service;

use App\Http\Models\Writer;

class WriterService {
    public function create(array $data) {
        return Writer::create([   
            'name' => $data['name'],
            'gender' => $data['gender'],
        ]);
    }

    public function update($uuid, array $data) {
        $writer_model = new Writer();
        $writer = $writer_model->getWriterByUuid($uuid);
        
        $writer->update([   
            'name' => $data['name'],
            'gender' => $data['gender'],
        ]);

        return $writer->fresh();
    }

    public function delete($uuid){
        $writer_model = new Writer();
        $writer = $writer_model->getWriterByUuid($uuid);

        $writer->delete();
    }
}
