<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Log as DebugLog;
use App\Models\Product;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            $action = $this->getAction($request);

            $user = $request->header('userName');

            // Ensure user is authenticated
            if (!$user) {
                return $next($request);; // Skip logging if not authenticated
            }
            // Retrieve model changes before processing the update
            $model = $this->getModelFromRoute($request);
            $originalData = $model ? $model: null;
            $details = $this->getLogDetails($request, $model, $action, $originalData);

            // Log entry
            Log::create([
                'user_id' => $user,
                'action' => $action,
                'model' => $model ? get_class($model) : null,
                'details' => $details,
            ]);
        }

        return $next($request);
    }

    /**
     * Determine the action (create, update, delete).
     */
    private function getAction(Request $request): string
    {
        return $request->isMethod('post') ? 'created' :
            ($request->isMethod('put') ? 'updated' : 'deleted');
    }

    /**
     * Get the model instance from the route parameters if applicable.
     */
    private function getModelFromRoute(Request $request)
    {

        $product = Product::find($request->route()->parameters());
        if ($product != null) {
            return $product;
        }

        return null;
    }

    /**
     * Get detailed log information.
     */
    private function getLogDetails(Request $request, $model, string $action, $originalData): string
    {
        if ($action === 'updated') {
            $changes= "";
            $newData = $request->all();
            foreach ($newData as $key => $newValue) {
                $originalAttributes = $originalData[0];
                $oldValue = $originalAttributes[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes .= "Attribute {$key} has been updated from {$oldValue} to {$newValue}.\n";
                }
            }
            return $changes;
        }

        return $action === 'deleted' ? ['model' => $model] : $request->all();
    }
}
