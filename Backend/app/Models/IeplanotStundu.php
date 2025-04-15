<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IeplanotStundu extends Model
{
    use HasFactory;
    
    protected $table = 'ieplanot_stundu';
    
    protected $fillable = [
        'kurssID',
        'datumsID',
        'skaitlis',
        'laiksID',
        'stundaID',
        'pasniedzejsID',
        'kabinetaID',
    ];

    protected $originalAttributes = [];

    protected static function booted()
    {
        static::retrieved(function ($model) {
            $model->originalAttributes = [
                'kurssID' => $model->kurssID,
                'stundaID' => $model->stundaID,
                'pasniedzejsID' => $model->pasniedzejsID,
            ];
        });

        static::saving(function ($model) {
            if ($model->exists) {
                if ($model->isDirty('kurssID') || $model->isDirty('stundaID') || $model->isDirty('pasniedzejsID')) {
                    $oldStundaAmount = StundaAmount::where('kurssID', $model->originalAttributes['kurssID'])
                        ->where('stundaID', $model->originalAttributes['stundaID'])
                        ->where('pasniedzejsID', $model->originalAttributes['pasniedzejsID'])
                        ->first();
                    
                    if ($oldStundaAmount) {
                        $oldStundaAmount->daudzums += 1;
                        $oldStundaAmount->save();
                    }
                    
                    $newStundaAmount = StundaAmount::where('kurssID', $model->kurssID)
                        ->where('stundaID', $model->stundaID)
                        ->where('pasniedzejsID', $model->pasniedzejsID)
                        ->first();
                    
                    if ($newStundaAmount) {
                        if ($newStundaAmount->daudzums <= 0) {
                            throw new \Exception('Šim mācību priekšmetam nav pietiekams stundu skaits.');
                        }
                        
                        $newStundaAmount->daudzums -= 1;
                        $newStundaAmount->save();
                    }
                }
            } else {
                $stundaAmount = StundaAmount::where('kurssID', $model->kurssID)
                    ->where('stundaID', $model->stundaID)
                    ->where('pasniedzejsID', $model->pasniedzejsID)
                    ->first();
                
                if ($stundaAmount) {
                    if ($stundaAmount->daudzums <= 0) {
                        throw new \Exception('Šim mācību priekšmetam nav pietiekams stundu skaits.');
                    }
                    
                    $stundaAmount->daudzums -= 1;
                    $stundaAmount->save();
                }
            }
        });

        static::deleted(function ($model) {
            $stundaAmount = StundaAmount::where('kurssID', $model->kurssID)
                ->where('stundaID', $model->stundaID)
                ->where('pasniedzejsID', $model->pasniedzejsID)
                ->first();
            
            if ($stundaAmount) {
                $stundaAmount->daudzums += 1;
                $stundaAmount->save();
            }
        });
    }

    public function kurss()
    {
        return $this->belongsTo(Kurss::class, 'kurssID');
    }
    
    public function datums()
    {
        return $this->belongsTo(Datums::class, 'datumsID');
    }
    
    public function laiks()
    {
        return $this->belongsTo(Laiks::class, 'laiksID');
    }
    
    public function stunda()
    {
        return $this->belongsTo(Stunda::class, 'stundaID');
    }
    
    public function pasniedzejs()
    {
        return $this->belongsTo(Pasniedzejs::class, 'pasniedzejsID');
    }
    
    public function kabinets()
    {
        return $this->belongsTo(Kabinets::class, 'kabinetaID');
    }
}