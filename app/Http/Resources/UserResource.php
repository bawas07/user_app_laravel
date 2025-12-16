<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $user = $request->user();
        $data = $this->resource;

        $canEdit = false;

        switch ($user->role) {
            case 'administrator':
                $canEdit = true;
                break;
            case 'manager':
                if($data->role === 'user') {
                    $canEdit = true;
                }
                break;
            case 'user':
                if($data->id == $user->id) {
                    $canEdit = true;
                }
                break;
        }

        return [
            'id' => $data->id,
            'email' => $data->email,
            'name' => $data->name,
            'role' => $data->role,
            'created_at' => $data->created_at,
            'orders_count' => $data->orders_counts,
            'can_edit' => $canEdit,
        ];
    }
}
