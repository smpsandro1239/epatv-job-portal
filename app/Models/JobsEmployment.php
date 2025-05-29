  **artifactId**: "fixed-jobs-employment-id"
  **title**: "Models/JobsEmployment.php"
  **contentType**: "text/x-php"
  ```php
  <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class JobsEmployment extends Model
    {
        protected $table = 'jobs_employment';
        protected $fillable = [
            'title',
            'description',
            'company_id',
            'category_id',
            'area_of_interest_id',
            'posted_by'
        ];
    }
