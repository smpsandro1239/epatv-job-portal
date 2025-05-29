  **artifactId**: "new-admin-controller-id"
  **title**: "Api/AdminController.php"
  **contentType**: "text/x-php"
  ```php
  <?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use App\Models\JobsEmployment; // Corrigido de JobEmployment'

    class AdminController extends Controller
    {
        public function dashboard()
        {
            $stats = [
                'total_users' => User::count(),
                'total_jobs' => JobsEmployment::count(),
                'candidates' => User::where('role', 'candidate')->count(),
                'employers' => User::where('role', 'employer')->count(),
            ];
            return response()->json($stats);
        }
    }
