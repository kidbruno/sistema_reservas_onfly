<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTripTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user(): void
    {
        $payload = [
            'nome' => 'ana',
            'idade' => 29,
            'email' => 'ana.@teste.com',
            'senha' => '123456'
        ];

        $response = $this->postJson('/api/user', $payload);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Usuário inserido com sucesso. Id: 1',
            ]);

        $this->assertDatabaseHas('usuarios', [
            'Id' => 1,
            'nome' => 'ana',
            'email' => 'ana.@teste.com',
            'status' => 'ativo',
        ]);
    }

    public function test_can_soft_delete_user_by_setting_status_cancelado(): void
    {
        $user = User::create([
            'nome' => 'João Souza',
            'idade' => 31,
            'email' => 'joao.@teste.com',
            'senha' => md5('123456'),
            'status' => 'ativo',
        ]);

        $response = $this->deleteJson('/api/user/delete/' . $user->Id);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Usuário inativado com sucesso.',
            ]);

        $this->assertDatabaseHas('usuarios', [
            'Id' => $user->Id,
            'status' => 'cancelado',
        ]);
    }

    public function test_can_create_trip_for_existing_user(): void
    {
        $user = User::create([
            'nome' => 'maria',
            'idade' => 27,
            'email' => 'maria.@teste.com',
            'senha' => md5('123456'),
            'status' => 'ativo',
        ]);

        $payload = [
            'usuario_id' => $user->Id,
            'destino' => 'São Paulo',
            'partida_de' => 'Recife',
            'retorno_de' => 'São Paulo',
            'data_viagem_ida' => '2026-04-10',
            'data_viagem_volta' => '2026-04-15',
        ];

        $response = $this->postJson('/api/trip', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Pedido de viagem 1 criado com sucesso.',
            ]);

        $this->assertDatabaseHas('viagens', [
            'Id' => 1,
            'usuario_id' => $user->Id,
            'destino' => 'São Paulo',
            'status' => 'solicitada',
        ]);
    }
}
