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
	protected $fileable = [];
	protected $filablePrefix = '';

	public function initializeFileAttributeable(){
		if(!isset($this->filablePrefix)){
			$this->filablePrefix = Str::studly(static::class);
		}
		foreach($this->fileable as $attr){
			$this->${'set'.Str::studly($attr).'Attribute'} = function($value){
				if(!empty($value)){
					if(gettype($value) === 'array'){
						return array_map(function($file) {
							$fileUuid = (string)Str::uuid();
							$fileRealName = $file->getClientOriginalName();
							$containSlash = strpos($this->filablePrefix, '/');
							if($containSlash === false){
								$folder_path = '/'.$this->filablePrefix.'/';
							}elseif($containSlash > 0){
								$folder_path = '/'.$this->filablePrefix;
							}
							if(!str_ends_with($folder_path, '/')){
								$folder_path = $folder_path.'/';
							}
							$path = $folder_path.Carbon::now()->format("Y-m-d");
							$fullPath = $path.$fileUuid.'-'.$fileRealName;
							$fileExtension = $file->getClientOriginalExtension();
							Storage::disk('s3')->put($fullPath, file_get_contents($file), 'public');

							return Storage::disk('s3')->url($fullPath);
						}, $value);
					}
					$fileUuid = (string)Str::uuid();
					$fileRealName = $value->getClientOriginalName();
					$containSlash = strpos($this->filablePrefix, '/');
					if($containSlash === false){
						$folder_path = '/'.$this->filablePrefix.'/';
					}elseif($containSlash > 0){
						$folder_path = '/'.$this->filablePrefix;
					}
					if(!str_ends_with($folder_path, '/')){
						$folder_path = $folder_path.'/';
					}
					$path = $folder_path.Carbon::now()->format("Y-m-d");
					$fullPath = $path.$fileUuid.'-'.$fileRealName;
					$fileExtension = $value->getClientOriginalExtension();
					Storage::disk('s3')->put($fullPath, file_get_contents($value), 'public');

					return Storage::disk('s3')->url($fullPath);
				}else{
					return null;
				}
			};
		}
	}
}
