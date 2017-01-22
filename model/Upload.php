<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Image;
use reg2005\UploadManager\UploadManager;

/**
 * Class Upload
 * @property string $name
 * @property string $description
 * @property string $disk
 * @property string $path
 * @property string $size
 * @property string $user_id
 * @package App
 */
class Upload extends Model
{
    use \Kalnoy\Nestedset\NodeTrait;
    protected $table = 'uploads';

    protected $appends = ['url', 'name'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function uploadable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        $manager = UploadManager::getInstance();
        $url = $manager->getUploadUrl($this->disk, $this->path);
        return $url;
    }

    public function getNameAttribute()
    {
        $nameExp = explode('/', $this->path);
        return end($nameExp);
    }

    public function save(array $options = [])
    {
        $saved = parent::save($options);
    }

    public function makeSizes()
    {
        if ($this->type !== 'image') {
            return $this;
        }

        $sizes = \Config::get('upload.validator_groups.image.sizes');

        foreach ($sizes as $name => $size) {
            $this->makeOneSize($name, $size);
        }

        $this->children();

        return $this;
    }

    public function makeOneSize($name, $size)
    {
        $findAnalog = self::query()
            ->where('parent_id', $this->id)
            ->where('width', $size['w'])
            ->where('height', $size['h'])
            ->count();

        if ($findAnalog) {
            return false;
        }

        $explode = explode('.', $this->file_name);
        $fileExtension = end($explode);
        $tmpfname = tempnam("/tmp", "UL_IMAGE") . '.' . $fileExtension;
        $img = \Image::make($this->url);
        $img->resize($size['w'], $size['h']);
        $img->save($tmpfname);

        $manager = UploadManager::getInstance();
        $upload = $manager->upload($tmpfname);
        $upload->user_id = \Auth::user()->id;
        $upload->watermarked = true;
        $upload->save();

        $this->children()->save($upload);
    }

    public function deleteFile($autoSave = true)
    {
        $this->children();

        if ($this->children) {
            foreach ($this->children as $children) {
                $children->deleteFile();
            }
        }

        if ($this->path) {
            $disk = \Storage::disk($this->disk);
            if ($disk->exists($this->path)) {
                $disk->delete($this->path);
                $this->path = '';
                if ($autoSave) {
                    $this->delete();
                }
            }
        }
    }

    public function isInDisk($diskName)
    {
        return $this->disk == $diskName ? true : false;
    }

    public function moveToDisk($newDiskName)
    {
        if ($newDiskName == $this->disk) {
            return true;
        }
        $currentDisk = \Storage::disk($this->disk);
        $content = $currentDisk->get($this->path);

        $newDisk = \Storage::disk($newDiskName);
        $newDisk->put($this->path, $content);
        if ($newDisk->exists($this->path)) {
            $this->disk = $newDiskName;
            $this->save();
            $currentDisk->delete($this->path);
            return true;
        }
        return false;
    }
}
