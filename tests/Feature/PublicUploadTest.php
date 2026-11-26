<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicUploadTest extends TestCase
{
    private string $uploadRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadRoot = sys_get_temp_dir() . '/solo-public-upload-test-' . uniqid();
        mkdir($this->uploadRoot . '/uploads', 0700, true);
        config(['filesystems.disks.public.root' => $this->uploadRoot]);
        Storage::forgetDisk('public');
    }

    protected function tearDown(): void
    {
        foreach (glob($this->uploadRoot . '/uploads/*') as $path) unlink($path);
        rmdir($this->uploadRoot . '/uploads');
        rmdir($this->uploadRoot);
        parent::tearDown();
    }

    public function test_existing_upload_is_served_from_public_storage(): void
    {
        file_put_contents($this->uploadRoot . '/uploads/photo.jpg', 'uploaded image');
        $response = $this->get('/storage/uploads/photo.jpg');
        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame(realpath($this->uploadRoot . '/uploads/photo.jpg'), $response->baseResponse->getFile()->getRealPath());
    }

    public function test_missing_and_symlinked_uploads_return_404(): void
    {
        $this->get('/storage/uploads/missing.jpg')->assertNotFound();
        symlink(__FILE__, $this->uploadRoot . '/uploads/link.jpg');
        $this->get('/storage/uploads/link.jpg')->assertNotFound();
    }
}
