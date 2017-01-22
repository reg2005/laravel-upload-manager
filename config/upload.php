<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:52
 */

return [
    
    /**
    * filesystems.php disks
    **/
    'base_storage_disk' => 'selectel', //or local
    
    /**
    *
    **/
    'upload_model'      => App\Upload::class,
    
    /**
    *
    **/
    'upload_strategy'   => reg2005\UploadManager\UploadStrategy::class,
    
    /**
    * validator group withValidator() common。
    **/
    'validator_groups'  => [
        'common' => [
            /**
            * http://laravel.com/docs/5.3/validation
            **/
            'min' => 0,  //kilobytes    
            'max' => 4096,  //kilobytes
        ],
        'image'  => [
            'max'   => 8192,  //kilobytes
            'mimes' => 'jpeg,bmp,png,gif',
            'sizes' => [
                'big' => [
                    'w' => 1024, 'h' => 768, 'type' => 'resize', 'watermark' => TRUE
                ],
                'small' => [
                    'w' => 640, 'h' => 480, 'type' => 'resize', 'watermark' => TRUE
                ],
                'square' => [
                    'w' => 250, 'h' => 330, 'type' => 'resize_crop', 'watermark' => FALSE
                ],
                'wide' => [
                    'w' => 900, 'h' => 280, 'type' => 'resize_crop', 'watermark' => FALSE
                ],
            ]
        ],
        'document'  => [
            'max'   => 8192,  //kilobytes
            'mimes' => 'pdf,doc,docx'
        ]
    ]
];