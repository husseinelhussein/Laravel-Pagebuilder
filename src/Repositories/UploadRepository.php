<?php

namespace HansSchouten\LaravelPageBuilder\Repositories;

use PHPageBuilder\Repositories\BaseRepository;
use PHPageBuilder\UploadedFile;

class UploadRepository extends BaseRepository
{

    /**
     * The uploads database table.
     *
     * @var string
     */
    protected $table = 'uploads';

    /**
     * The class that represents each uploaded file.
     *
     * @var string
     */
    protected $class = UploadedFile::class;

    /**
     * Create a new uploaded file.
     *
     * @param array $data
     * @return bool|object
     */
    public function create(array $data)
    {
        $fields = ['multi_saas_id', 'public_id', 'original_file', 'mime_type', 'server_file'];
        $stop = null;
        foreach ($fields as $field) {
            if(!isset($data[$field])){
                return false;
            }
            if (! is_string($data[$field]) && ! is_integer($data[$field])) {
                return false;
            }
        }
        $res = parent::create([
            'multi_saas_id' => $data['multi_saas_id'],
            'public_id' => $data['public_id'],
            'original_file' => $data['original_file'],
            'mime_type' => $data['mime_type'],
            'server_file' => $data['server_file'],
        ]);
        return $res;
    }
}
