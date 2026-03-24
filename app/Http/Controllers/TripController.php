<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripStatusUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TripController extends Controller
{
    public function travelRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'usuario_id'       => ['required', 'integer', 'exists:usuarios,Id'],
            'destino'          => ['required', 'string', 'max:100'],
            'partida_de'       => ['required', 'string', 'max:100'],
            'retorno_de'       => ['required', 'string', 'max:100'],
            'data_viagem_ida'  => ['required', 'date'],
            'data_viagem_volta'=> ['required', 'date', 'after_or_equal:data_viagem_ida'],
        ]);

        $trip = Trip::create([
            ...$validated,
            'status' => Trip::STATUS_SOLICITADO,
        ]);

        return response()->json([
            'message' => 'Pedido de viagem ' . $trip->Id . ' criado com sucesso.',
        ], Response::HTTP_CREATED);
    }

    public function getTravelById(int $id): JsonResponse
    {
        $trip = Trip::find($id);

        if (! $trip) {
            return response()->json([
                'message' => 'Pedido de viagem não encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'viagem' => $trip,
        ], Response::HTTP_OK);
    }

    public function getAllTravels(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status'           => ['nullable', Rule::in([
                Trip::STATUS_SOLICITADO,
                Trip::STATUS_APROVADO,
                Trip::STATUS_CANCELADO,
            ])],
            'destino'          => ['nullable', 'string', 'max:100'],
            'partida_de'       => ['nullable', 'string', 'max:100'],
            'retorno_de'       => ['nullable', 'string', 'max:100'],
            'data_viagem_ida'  => ['nullable', 'date'],
            'data_viagem_volta'=> ['nullable', 'date', 'after_or_equal:data_viagem_ida'],
        ]);

        $query = Trip::query()->orderByDesc('Id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['destino'])) {
            $query->where('destino', 'like', '%'.$validated['destino'].'%');
        }

        if (! empty($validated['partida_de'])) {
            $query->where('partida_de', 'like', '%'.$validated['partida_de'].'%');
        }

        if (! empty($validated['retorno_de'])) {
            $query->where('retorno_de', 'like', '%'.$validated['retorno_de'].'%');
        }

        if (! empty($validated['data_viagem_ida'])) {
            $query->whereDate('data_viagem_ida', '>=', $validated['data_viagem_ida']);
        }

        if (! empty($validated['data_viagem_volta'])) {
            $query->whereDate('data_viagem_volta', '<=', $validated['data_viagem_volta']);
        }

        return response()->json([
            'viagens' => $query->get([
                'Id',
                'destino',
                'status',
                'partida_de',
                'retorno_de',
                'data_viagem_ida',
                'data_viagem_volta',
                'usuario_id',
            ]),
        ], Response::HTTP_OK);
    }

    public function updateStatusTravel(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status'     => ['required', Rule::in([
                Trip::STATUS_APROVADO,
                Trip::STATUS_CANCELADO,
            ])],
            'usuario_id' => ['required', 'integer', 'exists:usuarios,Id'],
        ]);

        $trip = Trip::find($id);

        if (! $trip) {
            return response()->json([
                'status' => false,
                'message' => 'Pedido de viagem não encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }

        $adminUser = User::find($validated['usuario_id']);

        if (! $adminUser || ! $this->isAdmin($adminUser)) {
            return response()->json([
                'status' => false,
                'message' => 'Somente um usuário administrador pode alterar o status.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ((int) $trip->usuario_id === (int) $adminUser->Id) {
            return response()->json([
                'status' => false,
                'message' => 'O solicitante não pode alterar o próprio pedido.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($validated['status'] === Trip::STATUS_CANCELADO && $trip->status === Trip::STATUS_APROVADO) {
            return response()->json([
                'status' => false,
                'message' => 'Não é permitido cancelar um pedido já aprovado.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $trip->status = $validated['status'];
        $trip->save();

        $this->notifyUser($trip);

        return response()->json([
            'status' => true,
            'message' => 'Status do pedido atualizado com sucesso.',
            'trip' => $trip,
        ], Response::HTTP_OK);
    }

    private function isAdmin(User $user): bool
    {
        return (bool) ($user->is_admin ?? false);
    }

    private function notifyUser(Trip $trip): void
    {
        if ($trip->usuario_id) {
            $user = User::find($trip->usuario_id);

            if ($user) {
                $user->notify(new TripStatusUpdated($trip));

                return;
            }
        }
    }
}