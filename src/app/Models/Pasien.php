<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\EncryptionHelper;

class Pasien extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pasiens';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'email',
    ];

    /**
     * Atribut yang harus dikonversi.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Data yang akan dienkripsi secara otomatis.
     *
     * @var array
     */
    protected $encryptable = [
        'no_telepon',
        'email',
        'alamat',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->encryptSensitiveData();
        });

        static::updating(function ($model) {
            $model->encryptSensitiveData();
        });
    }

    /**
     * Enkripsi data sensitif
     */
    protected function encryptSensitiveData()
    {
        foreach ($this->encryptable as $field) {
            if (!empty($this->attributes[$field]) && !$this->isEncrypted($this->attributes[$field])) {
                $this->attributes[$field] = EncryptionHelper::encrypt($this->attributes[$field]);
            }
        }
    }

    /**
     * Cek apakah string sudah terenkripsi
     */
    protected function isEncrypted($value)
    {
        // Metode sederhana untuk mengecek apakah string sudah terenkripsi
        // Biasanya string terenkripsi dimulai dengan karakter khusus
        return (strpos($value, 'eyJ') === 0);
    }

    /**
     * Get the no_telepon attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getNoTeleponAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->isEncrypted($value) ? EncryptionHelper::decrypt($value) : $value;
    }

    /**
     * Get the email attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getEmailAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->isEncrypted($value) ? EncryptionHelper::decrypt($value) : $value;
    }

    /**
     * Get the alamat attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getAlamatAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->isEncrypted($value) ? EncryptionHelper::decrypt($value) : $value;
    }

    /**
     * Mendapatkan rekam medis untuk pasien ini.
     */
    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class);
    }

    /**
     * Mendapatkan usia pasien.
     *
     * @return int
     */
    public function getUsiaAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }
}
