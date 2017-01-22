# laravel-upload-manager
Upload, validate, storage, manage by API for Laravel 5.1/5.2

## Requirement

1. Laravel 5.1/5.2

## Install

1. composer require reg2005/laravel-upload-manager
2. ```config/app.php```
```
'providers' => [
    'reg2005\UploadManager\UploadManagerServiceProvider',
    Intervention\Image\ImageServiceProvider::class
]

...

'aliases' => [
    'Image' => Intervention\Image\Facades\Image::class
]
```
3. php artisan vendor:publish --provider="reg2005\UploadManager\UploadManagerServiceProvider"
4. php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravel5"
5. php artisan migrate
6. Done

## Usage

1. Upload and store a file.
    
    ```php
     
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $file = $request->file('avatar');
                $manager = UploadManager::getInstance();
                $upload = $manager->upload($file);
                $upload->save();
                return $upload;
            }
        }
    ```
 
2. Fetch and store a file from a URL
    
    ```php
     
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $fileUrl = $request->input('url');
                $manager = UploadManager::getInstance();
                $upload = $manager->upload($fileUrl);
                $upload->save();
                return $upload;
            }
        }
    ```
 
3. Update a upload object
    
    ```php
     
        use App\Upload;
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $uploadId = $request->input('id');
                $file = $request->file('avatar');
                
                $manager = UploadManager::getInstance();
                $upload = Upload::find($uploadId);
                if($manager->update($upload, $file))
                {
                    $upload->save();
                    return $upload;
                }
                return ['result'=>false];
            }
        }
    ```
 
4. Update a upload object from a URL
    
    ```php
     
        use App\Upload;
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $uploadId = $request->input('id');
                $fileUrl = $request->input('url');
                
                $manager = UploadManager::getInstance();
                $upload = Upload::find($uploadId);
                if($manager->update($upload, $fileUrl))
                {
                    $upload->save();
                    return $upload;
                }
                return ['result'=>false];
            }
        }
    ```
    
5. Validation
    
    ```php
    
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $file = $request->file('avatar');
                $manager = UploadManager::getInstance();
                $upload = $manager->withValidator('image')->upload($file);    //加上验证组
                
                if($upload)
                {
                    $upload->save();
                    return $upload;
                }
                else
                {
                    $errorMessages = $manager->getErrors();                   //得到所有错误信息
                    $errorMessage = $manager->getFirstErrorMessage();         //得到第一条错误信息
                    throw new \Exception($errorMessage);
                }
            }
        }
    ```
    
6. Disk 
    
    ```php
    
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $file = $request->file('avatar');
                $manager = UploadManager::getInstance();
                $upload = $manager
                    ->withValidator('image')
                    ->withDisk('selectel')         // 储存到七牛磁盘里
                    ->upload($file);
                $upload->save();
                return $upload;
            }
        }
    ```
    
7. ``` $upload ``` 
    
    ```php
    
        use reg2005\UploadManager\UploadManager;
        
        class UploadController extend Controller
        {
            public function postUpload(Request $request)
            {
                $file = $request->file('avatar');
                $manager = UploadManager::getInstance();
                $upload = $manager
                    ->withValidator('image')
                    ->withDisk('localhost')
                    ->upload($file, function($upload){
                        if($upload->size > 1024*1024)
                        {
                            $upload->disk = 'selectel';
                        }
                        return $upload;
                    });
                $upload->save();
                return $upload;
            }
        }
    ```
    
## Configuration

1. ``` config/upload.php ```

2. ``` App\Upload ```
    
3. ``` UploadStrategy.php ```
    
    ```php
        
        <?php namespace App\Extensions;
        
        use reg2005\UploadManager\UploadStrategy as BaseUploadStrategy;
        use reg2005\UploadManager\UploadStrategyInterface;
        
        class UploadStrategy extends BaseUploadStrategy implements UploadStrategyInterface
        {
        
            /**
             * @param $filename
             * @return string
             */
            public function makeStorePath($filename)
            {
                $path = 'uploads/' . $filename;
                return $path;
            }
        
            /**
             * disk localuploads
             * @param $path
             * @return string
             */
            public function getLocaluploadsUrl($path)
            {
                $url = url('uploads/' . $path);
                return $url;
            }
        
            /**
             * disk selectel
             * @param $path
             * @return string
             */
            public function getselectelUrl($path)
            {
                $url = 'http://' . trim(\Config::get('filesystems.disks.selectel.domain'), '/') . '/' . trim($path, '/');
                return $url;
            }
        } 
    ```
    
     ``` config/upload.php ``` 
     ``` upload_strategy ```
    
  

    
