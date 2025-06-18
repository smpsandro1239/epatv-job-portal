  **artifactId**: "application-model-id"
  **title**: "Application.php"
  **contentType**: "text/x-php"
  ```php
  <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Application extends Model
    {
        use HasFactory;

        protected $table = 'applications';
        protected $fillable = [
            'user_id',
            'job_id',
            'status',
            'name',
            'email',
            'phone',
            'course_completion_year',
            'cv_path',
            'message', // Renamed from cover_letter
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function job()
        {
            return $this->belongsTo(Job::class, 'job_id');
        }
    }
