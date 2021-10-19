<?php
/**
 * Created by PhpStorm.
 * User: taejong
 * Date: 1/9/20
 * Time: 11:14 PM
 */

namespace Blocksystems\FileAttributes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileAttributeable{
	public function initializeFileAttributeable(){
		if(($this->fileableSync ?? false) && isset($this->fileable)){
			foreach($this->fileable as $attr){
				$this->${'set'.Str::studly($attr).'Attribute'} = function($value) use ($attr){
					$this->uploadFileAttribute($attr, $value);
					return $this->{$attr};
				};
			}
		}
	}

	public static function bootFileAttributeable(){
		static::creating(
			function($model){
				if(isset($model->fileable)){
					foreach($model->fileable as $attr){
						$model->uploadFileAttribute($attr);
					}
				}
			}
		);
		static::saving(
			function($model){
				if(isset($model->fileable) && !($model->fileableSync ?? false)){
					foreach($model->fileable as $attr){
						if($model->fileAutoDeleting ?? true){
							$model->deleteFileAttribute($attr, true);
						}
						$model->uploadFileAttribute($attr);
					}
				}
			}
		);
		static::updating(
			function($model){
				if($model->fileable){
					foreach($model->fileable as $attr){
						if($model->fileAutoDeleting ?? true){
							$model->deleteFileAttribute($attr, true);
						}
						$model->uploadFileAttribute($attr);
					}
				}
			}
		);
		static::deleting(function($model){
			if(($model->fileAutoDeleting ?? true) && isset($model->fileable)){
				foreach($model->fileable as $attr){
					$model->deleteFileAttribute($attr);
				}
			}
		});
	}

	private function getPath($attr){
		$path = (isset($this->fileablePrefix) ? $this->fileablePrefix.'/' : '').($this->fileablePath ?? (Str::studly(class_basename(static::class)).'/'.$attr.'/'));
		$containSlash = strpos($path, '/');
		if($containSlash === false){
			return '/'.$path.'/';
		}elseif($containSlash > 0){
			$containSlash = strrpos($path, '/');
			if($containSlash < mb_strlen($path) - 1){
				return '/'.$path.'/';
			}else{
				return '/'.$path;
			}
		}else{
			$containSlash = strrpos($path, '/');
			if($containSlash < mb_strlen($path) - 1){
				return $path.'/';
			}else{
				return $path;
			}
		}
	}

	private function uploadFileAttribute($attr, $file = null){
		if(isset($file)){
			if(isset($attr) && isset($file) && $this->getOriginal($attr) != $file){
				$path = $this->getPath($attr);
				if(gettype($file) === 'object'){
					$fileUuid = (string)Str::uuid();
					$fileRealName = $file->getClientOriginalName();
					$path .= Carbon::now()->format("Y-m-d");
					$fullPath = $path.$fileUuid.'-'.$fileRealName;
					$this->{$attr} = Storage::disk()->put($fullPath, file_get_contents($this->{$attr}), 'public') ? Storage::disk()->url($fullPath) : null;
				}elseif(gettype($file) === 'array'){
					$this->{$attr} = array_map(function($file) use ($path){
						if(gettype($file) === 'object'){
							$fileUuid = (string)Str::uuid();
							$fileRealName = $file->getClientOriginalName();
							$path .= Carbon::now()->format("Y-m-d");
							$fullPath = $path.$fileUuid.'-'.$fileRealName;

							return Storage::disk()->put($fullPath, file_get_contents($file), 'public') ? Storage::disk()->url($fullPath) : null;
						}else{
							return $file;
						}
					}, $file);
				}
			}elseif(!isset($attr) || !isset($file)){
				$this->{$attr} = null;
			}
		}else{
			if(isset($attr) && isset($this->{$attr}) && $this->getOriginal($attr) != $this->{$attr}){
				$path = $this->getPath($attr);
				if(gettype($this->{$attr}) === 'object'){
					$fileUuid = (string)Str::uuid();
					$fileRealName = $this->{$attr}->getClientOriginalName();
					$path .= Carbon::now()->format("Y-m-d");
					$fullPath = $path.$fileUuid.'-'.$fileRealName;
					$this->{$attr} = Storage::disk()->put($fullPath, file_get_contents($this->{$attr}), 'public') ? Storage::disk()->url($fullPath) : null;
				}elseif(gettype($this->{$attr}) === 'array'){
					$this->{$attr} = array_map(function($file) use ($path){
						if(gettype($file) === 'object'){
							$fileUuid = (string)Str::uuid();
							$fileRealName = $file->getClientOriginalName();
							$path .= Carbon::now()->format("Y-m-d");
							$fullPath = $path.$fileUuid.'-'.$fileRealName;

							return Storage::disk()->put($fullPath, file_get_contents($file), 'public') ? Storage::disk()->url($fullPath) : null;
						}else{
							return $file;
						}
					}, $this->{$attr});
				}
			}elseif(!isset($attr) || !isset($this->{$attr})){
				$this->{$attr} = null;
			}
		}
	}

	private function deleteFileAttribute($attr, $isOriginal = false){
		if($isOriginal){
			if($this->getOriginal($attr) !== null){
				if(gettype($this->getOriginal($attr)) === 'string'){
					Storage::delete($this->getOriginal($attr));
				}elseif(gettype($this->getOriginal($attr)) === 'array'){
					$this->getOriginal($attr)->each(function($path){
						Storage::delete($path);
					});
				}
			}
		}elseif(isset($this->{$attr})){
			if(gettype($this->{$attr}) === 'string'){
				Storage::delete($this->{$attr});
			}elseif(gettype($this->{$attr}) === 'array'){
				$this->{$attr}->each(function($path){
					Storage::delete($path);
				});
			}
		}
	}
}
