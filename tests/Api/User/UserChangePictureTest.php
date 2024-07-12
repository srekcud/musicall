<?php

namespace Api\User;

use App\Tests\ApiTestCase;
use App\Tests\Factory\User\UserFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class UserChangePictureTest extends ApiTestCase
{
    public function test_change_picture(): void
    {
        $user1 = UserFactory::new()->asBaseUser()->create()->_real();
        $file = new UploadedFile(__DIR__ . '/fixtures/image-ok.jpeg', 'image-ok.jpeg');

        $this->assertNull($user1->getProfilePicture());

        $this->client->loginUser($user1);
        $this->client->request('POST', '/api/user_profile_pictures', [], ['imageFile' => $file], [
            'CONTENT_TYPE' => 'multipart/form-data',
        ], );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertNotNull($user1->getProfilePicture());
    }

    public function test_change_picture_too_big(): void
    {
        $user1 = UserFactory::new()->asBaseUser()->create()->_real();
        $file = new UploadedFile(__DIR__ . '/fixtures/image-too-big.jpg', 'image-too-big.jpg');

        $this->client->loginUser($user1);
        $this->client->request('POST', '/api/user_profile_pictures', [], ['imageFile' => $file], [
            'CONTENT_TYPE' => 'multipart/form-data',
        ], );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSame('{"@id":"\/api\/validation_errors\/7f87163d-878f-47f5-99ba-a8eb723a1ab2","@type":"ConstraintViolationList","status":422,"violations":[{"propertyPath":"image_file","message":"La largeur de l\u0027image est trop grande (4100px). La largeur maximale autorisée est de 4000px.","code":"7f87163d-878f-47f5-99ba-a8eb723a1ab2"}],"detail":"image_file: La largeur de l\u0027image est trop grande (4100px). La largeur maximale autorisée est de 4000px.","hydra:title":"An error occurred","hydra:description":"image_file: La largeur de l\u0027image est trop grande (4100px). La largeur maximale autorisée est de 4000px.","type":"\/validation_errors\/7f87163d-878f-47f5-99ba-a8eb723a1ab2","title":"An error occurred"}', $this->client->getResponse()->getContent());
    }

    public function test_change_picture_too_small(): void
    {
        $user1 = UserFactory::new()->asBaseUser()->create()->_real();
        $file = new UploadedFile(__DIR__ . '/fixtures/image-too-small.jpeg', 'image-too-small.jpeg');

        $this->client->loginUser($user1);
        $this->client->request('POST', '/api/user_profile_pictures', [], ['imageFile' => $file], [
            'CONTENT_TYPE' => 'multipart/form-data',
        ], );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSame('{"@id":"\/api\/validation_errors\/9afbd561-4f90-4a27-be62-1780fc43604a","@type":"ConstraintViolationList","status":422,"violations":[{"propertyPath":"image_file","message":"La largeur de l\u0027image est trop petite (200px). La largeur minimale attendue est de 450px.","code":"9afbd561-4f90-4a27-be62-1780fc43604a"}],"detail":"image_file: La largeur de l\u0027image est trop petite (200px). La largeur minimale attendue est de 450px.","hydra:title":"An error occurred","hydra:description":"image_file: La largeur de l\u0027image est trop petite (200px). La largeur minimale attendue est de 450px.","type":"\/validation_errors\/9afbd561-4f90-4a27-be62-1780fc43604a","title":"An error occurred"}', $this->client->getResponse()->getContent());
    }

    public function test_change_picture_not_logged(): void
    {
        $file = new UploadedFile(__DIR__ . '/fixtures/image-ok.jpeg', 'image-ok.jpeg');
        $this->client->request('POST', '/api/user_profile_pictures', [], ['file' => $file], [
            'CONTENT_TYPE' => 'multipart/form-data',
        ], );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertSame('{"code":401,"message":"JWT Token not found"}', $this->client->getResponse()->getContent());
    }
}