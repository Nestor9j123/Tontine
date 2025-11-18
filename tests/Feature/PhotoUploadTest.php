<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function user_can_create_product_with_photos()
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $photos = [
            UploadedFile::fake()->image('product1.jpg', 800, 600),
            UploadedFile::fake()->image('product2.png', 600, 400)
        ];

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 50000,
            'duration_value' => 12,
            'duration_unit' => 'months',
            'type' => 'monthly',
            'status' => 'active',
            'photos' => $photos
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Produit créé !');

        // Vérifier que le produit a été créé
        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product);

        // Vérifier que les photos ont été uploadées
        $this->assertEquals(2, $product->photos()->count());

        // Vérifier que la première photo est marquée comme principale
        $primaryPhoto = $product->photos()->where('is_primary', true)->first();
        $this->assertNotNull($primaryPhoto);

        // Vérifier que les fichiers existent
        Storage::disk('public')->assertExists($primaryPhoto->path);
    }

    /** @test */
    public function user_can_update_product_with_new_photos()
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        // Créer un produit existant
        $product = Product::factory()->create();

        $newPhotos = [
            UploadedFile::fake()->image('new_product1.jpg', 800, 600),
            UploadedFile::fake()->image('new_product2.png', 600, 400)
        ];

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'name' => $product->name,
            'price' => $product->price,
            'duration_value' => 12,
            'duration_unit' => 'months',
            'type' => 'monthly',
            'status' => 'active',
            'photos' => $newPhotos
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Produit mis à jour !');

        // Recharger le produit
        $product->refresh();

        // Vérifier que les nouvelles photos ont été ajoutées
        $this->assertEquals(2, $product->photos()->count());
    }

    /** @test */
    public function user_can_create_user_with_photo()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $photo = UploadedFile::fake()->image('user_avatar.jpg', 300, 300);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'agent',
            'is_active' => true,
            'photo' => $photo
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'Utilisateur créé !');

        // Vérifier que l'utilisateur a été créé avec la photo
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->photo);

        // Vérifier que le fichier existe
        Storage::disk('public')->assertExists($user->photo);
    }

    /** @test */
    public function invalid_file_type_is_rejected()
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 50000,
            'duration_value' => 12,
            'duration_unit' => 'months',
            'type' => 'monthly',
            'status' => 'active',
            'photos' => [$invalidFile]
        ]);

        $response->assertSessionHasErrors('photos.0');
        $this->assertEquals(0, Product::count());
    }

    /** @test */
    public function file_size_limit_is_enforced()
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        // Créer un fichier de 3MB (> 2MB limit)
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(3072);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 50000,
            'duration_value' => 12,
            'duration_unit' => 'months',
            'type' => 'monthly',
            'status' => 'active',
            'photos' => [$largeFile]
        ]);

        $response->assertSessionHasErrors('photos.0');
        $this->assertEquals(0, Product::count());
    }

    /** @test */
    public function photo_upload_works_without_photos()
    {
        $user = User::factory()->create();
        $user->assignRole('secretary');

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product Without Photos',
            'price' => 50000,
            'duration_value' => 12,
            'duration_unit' => 'months',
            'type' => 'monthly',
            'status' => 'active'
            // Pas de photos
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Produit créé !');

        // Vérifier que le produit a été créé sans photos
        $product = Product::where('name', 'Test Product Without Photos')->first();
        $this->assertNotNull($product);
        $this->assertEquals(0, $product->photos()->count());
    }
}
