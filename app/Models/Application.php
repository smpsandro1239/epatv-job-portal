  **artifactId**: "application-model-id"
  **title**: "Application.php"
  **contentType**: "text/x-php"
  ```php
  <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Application extends Model
    {
        protected $table = 'applications';
        protected $fillable = ['user_id', 'job_id', 'status', 'cover_letter'];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function job()
        {
            return $this->belongsTo(JobsEmployment::class, 'job_id');
        }
    }
