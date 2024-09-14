<?php
use App\Models\User;
use App\Models\Vault;

it('allows a user to create a vault', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/vaults', ['name' => 'My Home Vault'])
        ->assertRedirect('/vaults');

    $this->assertDatabaseHas('vaults', [
        'name' => 'My Home Vault',
        'user_id' => $user->id,
    ]);
});

it('prevents creating a vault without a name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/vaults', ['name' => ''])
        ->assertSessionHasErrors(['name']);
});
